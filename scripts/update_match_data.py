"""Refresh the local Fanatik snapshot: python3 scripts/update_match_data.py.

The site serves this snapshot; it does not request Fanatik from visitors' browsers.
"""
import argparse
import json
from datetime import datetime, timezone
from pathlib import Path
from urllib.request import Request, urlopen

ROOT = Path(__file__).resolve().parent.parent
BASE = 'https://fast.fanatik.com.tr/api/v1/basketball'
TEAM = 'sr:competitor:833648'
LEAGUE = 'sr:competition:35430'


def load(endpoint, saved):
    if saved:
        payload = json.loads(Path(saved).read_text())
    else:
        request = Request(BASE + endpoint, headers={'User-Agent': 'Mozilla/5.0'})
        with urlopen(request, timeout=30) as response:
            payload = json.load(response)
    if not payload.get('isSuccess') or payload.get('data') is None:
        raise ValueError('Fanatik returned no valid data; existing snapshot preserved.')
    return payload['data']


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument('--fixtures-file')
    parser.add_argument('--standings-file')
    args = parser.parse_args()
    fixtures = load('/leagues/any/fixtures/' + TEAM, args.fixtures_file)
    standings = load('/leagues/' + LEAGUE + '/seasons/latest/standings/detailed', args.standings_file)
    total = next(item for item in standings['standings'] if item['type'] == 'total')
    group = next(g for g in total['groups'] if any(s['competitor']['id'] == TEAM for s in g['standings']))
    record = next(s for s in group['standings'] if s['competitor']['id'] == TEAM)
    matches = []
    for item in fixtures:
        if item['seasonId'] != standings['seasonId']:
            continue
        event, status = item['sportEvent'], item['sportEventStatus']
        teams = {t['qualifier']: t for t in event['competitors']}
        if TEAM not in [t['id'] for t in teams.values()]:
            continue
        matches.append({
            'id': event['id'], 'startTime': event['startTime'],
            'confirmed': event.get('startTimeConfirmed', False),
            'round': event['sportEventContext'].get('round', {}).get('number'),
            'home': teams['home']['displayName'] or teams['home']['name'],
            'away': teams['away']['displayName'] or teams['away']['name'],
            'isHome': teams['home']['id'] == TEAM,
            'venue': (event.get('venue') or {}).get('name'),
            'status': status['status'], 'homeScore': status.get('homeScore'),
            'awayScore': status.get('awayScore'),
            'won': ((status.get('homeScore') or 0) > (status.get('awayScore') or 0)) if teams['home']['id'] == TEAM else ((status.get('awayScore') or 0) > (status.get('homeScore') or 0)),
        })
    if not matches:
        raise ValueError('Current season fixtures are empty; existing snapshot preserved.')
    completed = [m for m in matches if m['status'] in ('closed', 'ended')]
    if any(not isinstance(m['homeScore'], (int, float)) or not isinstance(m['awayScore'], (int, float)) for m in completed):
        raise ValueError('Completed match scores are missing; existing snapshot preserved.')
    calculated = {
        'played': len(completed),
        'win': sum(m['won'] for m in completed),
        'loss': sum(not m['won'] for m in completed),
        'pointsFor': sum(m['homeScore'] if m['isHome'] else m['awayScore'] for m in completed),
        'pointsAgainst': sum(m['awayScore'] if m['isHome'] else m['homeScore'] for m in completed),
    }
    if any(record[key] != value for key, value in calculated.items()):
        raise ValueError('Fixtures and standings are inconsistent; existing snapshot preserved. Retry after the source updates.')
    snapshot = {
        'source': 'https://www.fanatik.com.tr/takim/harem-spor/basketbol/fikstur/',
        'updatedAt': datetime.now(timezone.utc).isoformat(),
        'season': group['name'],
        'standing': {key: record[key] for key in ['rank', 'played', 'win', 'loss', 'points', 'pointsFor', 'pointsAgainst']},
        'matches': sorted(matches, key=lambda m: m['startTime']),
    }
    output = 'window.HAREM_MATCH_DATA = ' + json.dumps(snapshot, ensure_ascii=False, indent=2) + ';\n'
    temporary = ROOT / 'assets/js/match-data.js.tmp'
    temporary.write_text(output)
    temporary.replace(ROOT / 'assets/js/match-data.js')
    print(f"Updated {len(matches)} matches for {group['name']}.")


if __name__ == '__main__':
    main()
