import AsyncStorage from '@react-native-async-storage/async-storage';
import React, { useEffect, useRef, useState } from 'react';
import { Animated, KeyboardAvoidingView, Platform, Pressable, StyleSheet, Text, TextInput, View } from 'react-native';
import { ApiError } from '../api/client';
import { useGiveaway, useJoinGiveaway } from '../api/hooks';
import type { Giveaway, MyEntry } from '../api/types';
import { useNow } from '../components/cards';
import { Icon } from '../components/Icon';
import { FadeIn, NATIVE, PressableScale } from '../components/motion';
import { ParallaxScreen } from '../components/Parallax';
import { Button, ErrorState, Loading, openUrl, Pill } from '../components/ui';
import { fullDate, longDate, normalizeInstagram, time } from '../lib/format';
import type { RootScreenProps } from '../navigation/types';
import { fonts, radius, space } from '../theme';
import { createStyles, useTheme } from '../themeContext';

const LAST_USERNAME = 'yh.last-instagram-username';
const pad = (n: number) => String(n).padStart(2, '0');

export function GiveawayScreen({ route }: RootScreenProps<'Giveaway'>) {
  const { c, T } = useTheme();
  const styles = useStyles();
  const { slug } = route.params;
  const { data: g, isPending, error, refetch } = useGiveaway(slug);

  if (isPending) {
    return <Loading variant="detail" />;
  }
  if (!g) {
    return <ErrorState error={error} onRetry={refetch} />;
  }
  const tone = g.status === 'completed' ? 'gold' : g.is_open ? 'green' : 'gray';

  return (
    <KeyboardAvoidingView style={{ flex: 1 }} behavior={Platform.OS === 'ios' ? 'padding' : undefined}>
      <ParallaxScreen
        image={g.image ? { uri: g.image } : null}
        height={380}
        onRefresh={refetch}
        testID="giveaway-screen"
        overlay={
          <View style={{ gap: 8 }}>
            <Pill text={g.status_text} tone={tone} live={g.is_open} />
            <Text style={styles.title} accessibilityRole="header">{g.title}</Text>
            <View style={styles.prizeRow}><Icon name="gift" size={18} color={c.gold} /><Text style={styles.prize}>{g.prize}</Text></View>
          </View>
        }>
        {g.description ? <Text style={[T.body, { marginTop: space.sm }]}>{g.description}</Text> : null}

        {g.status === 'active' && g.is_open && g.ends_at ? <FadeIn><Countdown iso={g.ends_at} winners={g.winner_count} /></FadeIn> : null}

        {g.status === 'completed' ? <Winners g={g} />
          : g.status === 'drawn' ? (
            <View style={styles.card}>
              <Text style={styles.cardTitle}>Çekiliş yapıldı</Text>
              <Text style={T.body}>Kazananların şartları sağladığı kontrol ediliyor. Sonuç kısa süre içinde burada duyurulacak.</Text>
            </View>
          ) : (
            <>
              {g.steps.length ? <FadeIn index={1}><Steps g={g} /></FadeIn> : null}
              {g.my_entry ? <MyEntryCard entry={g.my_entry} />
                : g.app_join ? <FadeIn index={2}><JoinForm slug={slug} requiresApp={g.requires_app} /></FadeIn>
                : !g.is_open ? (
                  <View style={styles.card}><Text style={T.body}>Katılım süresi doldu. Kazananlar yakında burada duyurulacak.</Text></View>
                ) : g.instagram_post_url ? (
                  <Button title="Instagram'da gönderiye git" icon="instagram" onPress={() => openUrl(g.instagram_post_url)} />
                ) : null}
            </>
          )}
      </ParallaxScreen>
    </KeyboardAvoidingView>
  );
}

function Countdown({ iso, winners }: { iso: string; winners: number }) {
  const { c, T } = useTheme();
  const styles = useStyles();
  const now = useNow(1000);
  const s = Math.max(0, Math.floor((Date.parse(iso) - now) / 1000));
  const units = [[Math.floor(s / 86400), 'GÜN'], [Math.floor((s % 86400) / 3600), 'SAAT'], [Math.floor((s % 3600) / 60), 'DAKİKA'], [s % 60, 'SANİYE']] as const;
  return (
    <View style={styles.countWrap} accessible accessibilityLabel={`Son katılıma ${units[0][0]} gün ${units[1][0]} saat ${units[2][0]} dakika`}>
      <View style={styles.countHead}>
        <Text style={T.kicker}>SON KATILIMA</Text>
        <Text style={[T.kicker, { color: c.gold }]}>{longDate(iso).split(' ').slice(0, 2).join(' ')} · {time(iso)}</Text>
      </View>
      <View style={styles.countRow}>
        {units.map(([v, l]) => (
          <View key={l} style={styles.countBox}>
            <Text style={styles.countValue}>{pad(v)}</Text>
            <Text style={styles.countLabel}>{l}</Text>
          </View>
        ))}
      </View>
      <Text style={[T.small, { textAlign: 'center' }]}>{winners} kişi kazanacak · kazananlar burada duyurulur</Text>
    </View>
  );
}

function Steps({ g }: { g: Giveaway }) {
  const { c } = useTheme();
  const styles = useStyles();
  const joined = !!g.my_entry;
  return (
    <View style={styles.card}>
      <Text style={styles.cardTitle}>Nasıl katılırım?</Text>
      {g.steps.map((s, i) => {
        const last = i === g.steps.length - 1;
        const done = joined && s.key === 'app_join';
        return (
          <View key={`${s.key}-${i}`} style={styles.step}>
            <View style={styles.stepRail}>
              <View style={[styles.stepDot, done && { backgroundColor: c.win, borderColor: c.win }]}>
                {done ? <Icon name="check" size={14} color={c.white} strokeWidth={3} /> : <Text style={styles.stepNum}>{i + 1}</Text>}
              </View>
              {!last ? <View style={styles.stepLine} /> : null}
            </View>
            <View style={[styles.stepBody, last && { paddingBottom: 0 }]}>
              <Text style={styles.stepText}>{s.text}</Text>
              {s.action ? (
                <PressableScale onPress={() => openUrl(s.action!.url)} accessibilityRole="link" style={styles.chip}>
                  <Text style={styles.chipText}>{s.action.label}</Text>
                  <Icon name="external" size={13} color={c.gold} />
                </PressableScale>
              ) : null}
            </View>
          </View>
        );
      })}
    </View>
  );
}

function JoinForm({ slug, requiresApp }: { slug: string; requiresApp: boolean }) {
  const { c, T } = useTheme();
  const styles = useStyles();
  const [username, setUsername] = useState('');
  const [consent, setConsent] = useState(false);
  const [focused, setFocused] = useState(false);
  const [localError, setLocalError] = useState<string | null>(null);
  const join = useJoinGiveaway(slug);
  const shake = useRef(new Animated.Value(0)).current;

  useEffect(() => {
    AsyncStorage.getItem(LAST_USERNAME).then(v => v && setUsername(prev => prev || v)).catch(() => undefined);
  }, []);

  const nudge = () => {
    shake.setValue(0);
    Animated.sequence([10, -10, 7, -7, 3, 0].map(v => Animated.timing(shake, { toValue: v, duration: 45, useNativeDriver: NATIVE }))).start();
  };

  const submit = () => {
    setLocalError(null);
    const u = normalizeInstagram(username);
    if (!u) {
      setLocalError('Geçerli bir Instagram kullanıcı adı yaz (harf, rakam, nokta, alt çizgi).');
      nudge();
      return;
    }
    if (!consent) {
      setLocalError('Devam etmek için onay kutusunu işaretle.');
      nudge();
      return;
    }
    join.mutate(
      { instagram_username: u, consent: true },
      { onSuccess: () => AsyncStorage.setItem(LAST_USERNAME, u).catch(() => undefined), onError: nudge },
    );
  };

  const serverError = join.error instanceof ApiError ? join.error.message : join.error ? 'Bir sorun oluştu, tekrar dene.' : null;
  const message = localError ?? serverError;
  const badInput = !!localError?.includes('kullanıcı adı') || (join.error instanceof ApiError && !!join.error.fieldErrors.instagram_username);

  return (
    <Animated.View style={[styles.card, styles.form, { transform: [{ translateX: shake }] }]}>
      <Text style={styles.cardTitle}>Çekilişe katıl</Text>
      {requiresApp ? (
        <View style={styles.note}>
          <Icon name="shield" size={18} color={c.greenGlow} />
          <Text style={styles.noteText}>Bu çekilişe sadece uygulamadan katılınır. Buradan gönderdiğin kayıt uygulama şartını sağlar. Her cihazdan tek hesapla katılabilirsin.</Text>
        </View>
      ) : null}
      {message ? <View style={styles.error} accessibilityRole="alert"><Icon name="alert" size={16} color={c.loss} /><Text style={styles.errorText}>{message}</Text></View> : null}

      <Text style={styles.inputLabel}>INSTAGRAM KULLANICI ADIN</Text>
      <View style={[styles.input, focused && styles.inputFocus, badInput && styles.inputBad]}>
        <Text style={styles.at}>@</Text>
        <TextInput
          testID="ig-input"
          value={username}
          onChangeText={setUsername}
          onFocus={() => setFocused(true)}
          onBlur={() => setFocused(false)}
          placeholder="kullaniciadi"
          placeholderTextColor={c.faint}
          autoCapitalize="none"
          autoCorrect={false}
          autoComplete="username"
          keyboardType={Platform.OS === 'ios' ? 'twitter' : 'default'}
          returnKeyType="done"
          onSubmitEditing={submit}
          maxLength={60}
          selectionColor={c.red}
          style={styles.textInput}
          accessibilityLabel="Instagram kullanıcı adın"
        />
      </View>

      <Pressable testID="consent" onPress={() => setConsent(c => !c)} style={styles.consent} accessibilityRole="checkbox" accessibilityState={{ checked: consent }}>
        <View style={[styles.box, consent && styles.boxOn]}>{consent ? <Icon name="check" size={14} color={c.white} strokeWidth={3} /> : null}</View>
        <Text style={styles.consentText}>Kullanıcı adımın bu çekiliş için kaydedilmesini ve kazanırsam yayınlanmasını kabul ediyorum.</Text>
      </Pressable>

      <Button testID="join-button" title="Çekilişe katıl" icon="gift" onPress={submit} loading={join.isPending} />
      <Text style={[T.small, { textAlign: 'center' }]}>Şartları sağlamayan katılımlar geçersiz sayılır.</Text>
    </Animated.View>
  );
}

function MyEntryCard({ entry }: { entry: MyEntry }) {
  const { c, T } = useTheme();
  const styles = useStyles();
  const pop = useRef(new Animated.Value(0)).current;
  useEffect(() => {
    Animated.spring(pop, { toValue: 1, useNativeDriver: NATIVE, speed: 8, bounciness: 14 }).start();
  }, [pop]);
  return (
    <FadeIn style={[styles.card, styles.joined]}>
      <Animated.View style={[styles.joinedIcon, { transform: [{ scale: pop }] }]}>
        <Icon name="check" size={34} color={c.white} strokeWidth={3} />
      </Animated.View>
      <Text style={styles.joinedTitle}>Katıldın!</Text>
      <Text style={styles.joinedUser}>@{entry.username}{entry.joined_at ? `  ·  ${fullDate(entry.joined_at)}` : ''}</Text>
      <Text style={[T.body, { textAlign: 'center' }]}>
        {entry.is_eligible
          ? 'Kaydın alındı ve şu an tüm otomatik şartları sağlıyorsun. Takip ve beğeni şartları kazanan seçildikten sonra kontrol edilir.'
          : entry.hint ?? 'Kaydın alındı. Diğer şartlar çekilişten önce kontrol edilecek.'}
      </Text>
      <Text style={[T.small, { textAlign: 'center' }]}>Bu cihazdan bu çekilişe başka bir hesapla katılamazsın.</Text>
    </FadeIn>
  );
}

function Winners({ g }: { g: Giveaway }) {
  const { c, T } = useTheme();
  const styles = useStyles();
  return (
    <View style={styles.card}>
      <View style={styles.winHead}><Icon name="trophy" size={22} color={c.gold} /><Text style={styles.cardTitle}>{g.winners.length > 1 ? 'Kazananlar' : 'Kazanan'}</Text></View>
      {g.winners.map((w, i) => (
        <FadeIn key={w.username} index={i}>
          <PressableScale onPress={() => openUrl(w.profile_url)} style={[styles.winner, i === 0 && styles.winnerFirst]} accessibilityRole="link" accessibilityLabel={`${i + 1}. kazanan @${w.username}`}>
            <Text style={[styles.winnerNum, i === 0 && { color: '#1A1205' }]}>{i + 1}</Text>
            <Text style={[styles.winnerName, i === 0 && { color: '#1A1205' }]} numberOfLines={1}>@{w.username}</Text>
            <Icon name="instagram" size={18} color={i === 0 ? '#1A1205' : c.muted} />
          </PressableScale>
        </FadeIn>
      ))}
      <Text style={T.small}>
        {g.eligible_count_at_draw ? `${g.eligible_count_at_draw} geçerli katılımcı arasından ` : ''}{g.drawn_at ? `${fullDate(g.drawn_at)} tarihinde çekildi. ` : ''}
        Kazananlarla Instagram'dan iletişime geçilecek. Kulübümüz sizden asla ödeme veya şifre istemez.
      </Text>
    </View>
  );
}

const useStyles = createStyles((c) => StyleSheet.create({
  title: { fontFamily: fonts.condBlack, fontSize: 40, lineHeight: 42, color: c.white, textTransform: 'uppercase' },
  prizeRow: { flexDirection: 'row', alignItems: 'center', gap: 8 },
  prize: { fontFamily: fonts.condBold, fontSize: 22, color: c.gold, textTransform: 'uppercase', flexShrink: 1 },
  card: { backgroundColor: c.surface, borderRadius: radius.xl, padding: space.lg, gap: space.md, borderWidth: StyleSheet.hairlineWidth, borderColor: c.line },
  cardTitle: { fontFamily: fonts.condBlack, fontSize: 22, color: c.text, textTransform: 'uppercase' },

  countWrap: { gap: space.md, marginTop: space.sm },
  countHead: { flexDirection: 'row', justifyContent: 'space-between' },
  countRow: { flexDirection: 'row', gap: space.sm },
  countBox: { flex: 1, alignItems: 'center', backgroundColor: c.surface, borderRadius: radius.lg, paddingVertical: space.md, borderTopWidth: 0 },
  countValue: { fontFamily: fonts.condBlack, fontSize: 40, lineHeight: 42, color: c.text, fontVariant: ['tabular-nums'] },
  countLabel: { fontFamily: fonts.condBold, fontSize: 12, letterSpacing: 1.5, color: c.muted },

  step: { flexDirection: 'row', gap: space.md },
  stepRail: { alignItems: 'center', width: 30 },
  stepDot: { width: 32, height: 32, borderRadius: 16, borderWidth: 1, borderColor: c.lineStrong, backgroundColor: c.bg, alignItems: 'center', justifyContent: 'center' },
  stepNum: { fontFamily: fonts.condBlack, fontSize: 15, color: c.text },
  stepLine: { flex: 1, width: 2, backgroundColor: c.lineStrong, marginVertical: 4 },
  stepBody: { flex: 1, gap: space.sm, paddingBottom: space.lg, paddingTop: 4 },
  stepText: { fontFamily: fonts.semibold, fontSize: 15, lineHeight: 22, color: c.text },
  chip: { flexDirection: 'row', alignItems: 'center', gap: 8, alignSelf: 'flex-start', borderWidth: 1, borderColor: 'rgba(229,169,60,0.4)', borderRadius: radius.pill, paddingHorizontal: 16, minHeight: 34 },
  chipText: { fontFamily: fonts.condBold, fontSize: 15, letterSpacing: 1, color: c.gold, textTransform: 'uppercase' },

  form: { borderColor: 'rgba(167,1,1,0.45)', borderWidth: 1 },
  note: { flexDirection: 'row', gap: space.sm, backgroundColor: 'rgba(22,194,106,0.08)', borderRadius: radius.md, padding: space.md, borderWidth: 1, borderColor: 'rgba(22,194,106,0.25)' },
  noteText: { flex: 1, fontFamily: fonts.regular, color: c.mode === 'dark' ? '#9BE7BE' : c.greenGlow, fontSize: 12, lineHeight: 16 },
  error: { flexDirection: 'row', gap: 8, alignItems: 'center', backgroundColor: 'rgba(240,70,70,0.12)', borderRadius: radius.md, padding: space.md },
  errorText: { flex: 1, fontFamily: fonts.semibold, color: c.mode === 'dark' ? '#FF8A8A' : c.loss, fontSize: 15 },
  inputLabel: { fontFamily: fonts.condBold, fontSize: 12, letterSpacing: 1.5, color: c.muted, marginBottom: -space.xs },
  input: { flexDirection: 'row', alignItems: 'center', borderWidth: 1.5, borderColor: c.lineStrong, borderRadius: radius.md, paddingHorizontal: space.md, height: 54, backgroundColor: c.surface2 },
  inputFocus: { borderColor: c.gold },
  inputBad: { borderColor: c.loss },
  at: { fontFamily: fonts.semibold, color: c.muted, fontSize: 22, marginRight: 4 },
  textInput: { flex: 1, fontFamily: fonts.semibold, fontSize: 22, color: c.text, paddingVertical: 0 },
  consent: { flexDirection: 'row', gap: space.md, alignItems: 'flex-start' },
  box: { width: 24, height: 24, borderRadius: 6, borderWidth: 2, borderColor: c.faint, alignItems: 'center', justifyContent: 'center', marginTop: 0 },
  boxOn: { backgroundColor: c.red, borderColor: c.red },
  consentText: { flex: 1, fontFamily: fonts.regular, fontSize: 15, lineHeight: 22, color: c.textSoft },

  joined: { alignItems: 'center', paddingVertical: space.xxl, borderColor: 'rgba(22,194,106,0.35)', borderWidth: 1 },
  joinedIcon: { width: 72, height: 72, borderRadius: 36, backgroundColor: c.win, alignItems: 'center', justifyContent: 'center' },
  joinedTitle: { fontFamily: fonts.condBlackItalic, fontSize: 40, color: c.text, textTransform: 'uppercase' },
  joinedUser: { fontFamily: fonts.condBold, fontSize: 15, letterSpacing: 0.5, color: c.gold, marginTop: -space.sm },

  winHead: { flexDirection: 'row', alignItems: 'center', gap: space.sm },
  winner: { flexDirection: 'row', alignItems: 'center', gap: space.md, backgroundColor: c.surface2, borderRadius: radius.md, padding: space.md },
  winnerFirst: { backgroundColor: c.gold },
  winnerNum: { fontFamily: fonts.condBlackItalic, fontSize: 22, width: 28, color: c.gold },
  winnerName: { flex: 1, fontFamily: fonts.condBlack, fontSize: 22, color: c.text },
}));
