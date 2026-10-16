<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Newsletter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NewsletterController extends Controller
{
    /**
     * Display a listing of newsletter subscribers.
     */
    public function index(Request $request)
    {
        $query = Newsletter::query();

        // Search filter
        if ($search = $request->input('search')) {
            $query->where('email', 'like', "%{$search}%");
        }

        // Status filter
        if ($status = $request->input('status')) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        }

        $subscribers = $query->latest()->paginate(25)->withQueryString();

        // Key metrics
        $totalCount = Newsletter::count();
        $activeCount = Newsletter::where('status', 'active')->count();
        $unsubscribedCount = Newsletter::where('status', 'unsubscribed')->count();
        $todayCount = Newsletter::whereDate('created_at', today())->count();

        // Comma-separated active emails for quick copy button
        $activeEmailsString = Newsletter::where('status', 'active')->pluck('email')->implode(', ');

        return view('admin.newsletters.index', compact(
            'subscribers',
            'totalCount',
            'activeCount',
            'unsubscribedCount',
            'todayCount',
            'activeEmailsString'
        ));
    }

    /**
     * Store a newly created subscriber or bulk emails from admin.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'notes' => 'nullable|string|max:500',
        ]);

        $input = $request->input('email');
        // Handle comma, space, or newline separated emails
        $emails = preg_split('/[\s,]+/', $input, -1, PREG_SPLIT_NO_EMPTY);
        $added = 0;
        $skipped = 0;

        foreach ($emails as $email) {
            $email = trim(strtolower($email));
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $existing = Newsletter::where('email', $email)->first();
                if (!$existing) {
                    Newsletter::create([
                        'email' => $email,
                        'ip_address' => $request->ip(),
                        'user_agent' => 'Admin Panel Ekleme',
                        'status' => 'active',
                        'notes' => $request->input('notes'),
                    ]);
                    $added++;
                } else {
                    if ($existing->status !== 'active') {
                        $existing->update(['status' => 'active']);
                        $added++;
                    } else {
                        $skipped++;
                    }
                }
            } else {
                $skipped++;
            }
        }

        $msg = "{$added} adet e-posta adresi başarıyla bülten listesine eklendi.";
        if ($skipped > 0) {
            $msg .= " ({$skipped} adet adres zaten kayıtlı veya geçersiz olduğu için atlandı).";
        }

        return redirect()->route('admin.newsletters.index')->with('success', $msg);
    }

    /**
     * Toggle the status between active and unsubscribed.
     */
    public function toggleStatus($id)
    {
        $subscriber = Newsletter::findOrFail($id);
        $newStatus = ($subscriber->status === 'active') ? 'unsubscribed' : 'active';
        $subscriber->update(['status' => $newStatus]);

        $statusText = ($newStatus === 'active') ? 'aktif edildi' : 'abonelikten çıkarıldı';
        return redirect()->back()->with('success', "'{$subscriber->email}' adresi {$statusText}.");
    }

    /**
     * Remove the specified subscriber from storage.
     */
    public function destroy($id)
    {
        $subscriber = Newsletter::findOrFail($id);
        $email = $subscriber->email;
        $subscriber->delete();

        return redirect()->route('admin.newsletters.index')->with('success', "'{$email}' adresi bülten listesinden silindi.");
    }

    /**
     * Export active subscribers as CSV file.
     */
    public function export(Request $request)
    {
        $status = $request->input('status', 'active');
        $query = Newsletter::query();
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $filename = 'yalovaharemspor-bulten-aboneleri-' . date('Y-m-d') . '.csv';

        $response = new StreamedResponse(function () use ($query) {
            $handle = fopen('php://output', 'w');
            // UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, ['ID', 'E-Posta', 'Durum', 'Kayıt Tarihi', 'IP Adresi', 'Notlar'], ';');

            $query->chunk(100, function ($subscribers) use ($handle) {
                foreach ($subscribers as $sub) {
                    fputcsv($handle, [
                        $sub->id,
                        $sub->email,
                        $sub->status === 'active' ? 'Aktif' : 'Abonelikten Çıktı',
                        $sub->created_at->format('d.m.Y H:i'),
                        $sub->ip_address ?? '—',
                        $sub->notes ?? '',
                    ], ';');
                }
            });

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }
}
