import React, { useState } from 'react';
import { Image, StyleSheet, Text, TextInput, View } from 'react-native';
import { ApiError } from '../api/client';
import { useClub, useSponsors, useSubscribe, useTicketPoints } from '../api/hooks';
import { Gradient } from '../components/Gradient';
import { Icon, IconName } from '../components/Icon';
import { FadeIn, PressableScale } from '../components/motion';
import { ParallaxScreen } from '../components/Parallax';
import { Button, ErrorState, Loading, openUrl, SectionHeader, Segmented } from '../components/ui';
import { fonts, radius, space } from '../theme';
import { createStyles, ThemePref, useTheme } from '../themeContext';

export function ClubScreen() {
  const { c, T } = useTheme();
  const styles = useStyles();
  const club = useClub();
  const sponsors = useSponsors();
  const points = useTicketPoints();
  const refresh = () => Promise.all([club.refetch(), sponsors.refetch(), points.refetch()]);

  if (club.isPending) {
    return <Loading variant="detail" />;
  }
  if (!club.data) {
    return <ErrorState error={club.error} onRetry={club.refetch} />;
  }
  const info = club.data;
  const socials = ([
    ['instagram', 'Instagram', info.social_instagram],
    ['x', 'X', info.social_x],
    ['youtube', 'YouTube', info.social_youtube],
    ['globe', 'Web sitesi', info.website],
  ] as [IconName, string, string | null][]).filter(([, , url]) => !!url);
  const identity = [
    [info.identity_green_title, info.identity_green_text, ['#0B5A31', '#03210F']],
    [info.identity_red_title, info.identity_red_text, ['#C20E0E', '#5A0000']],
    [info.identity_mascot_title, info.identity_mascot_text, ['#B8801E', '#4A3108']],
  ].filter(([t]) => !!t) as [string, string | null, [string, string]][];

  return (
    <ParallaxScreen
      image={info.about_image_2 || info.hero_image ? { uri: (info.about_image_2 || info.hero_image)! } : null}
      height={340}
      onRefresh={refresh}
      testID="club-screen"
      overlay={
        <View style={styles.hero}>
          <Image source={{ uri: info.logo }} style={styles.logo} />
          <Text style={styles.city}>{info.city_name}</Text>
          <Text style={styles.name}>{info.club_name}</Text>
          {info.footer_hashtag ? <Text style={styles.hashtag}>{info.footer_hashtag}</Text> : null}
        </View>
      }>
      {info.about_title ? <FadeIn><Text style={styles.motto}>{info.about_title.replace(/\n/g, ' ')}</Text></FadeIn> : null}

      {socials.length ? (
        <FadeIn index={1} style={styles.socials}>
          {socials.map(([icon, label, url]) => (
            <PressableScale key={label} onPress={() => openUrl(url)} accessibilityRole="link" accessibilityLabel={label} style={styles.social}>
              <Icon name={icon} size={22} color={c.text} />
              <Text style={styles.socialText}>{label}</Text>
            </PressableScale>
          ))}
        </FadeIn>
      ) : null}

      {identity.length ? (
        <View style={{ gap: space.md }}>
          <SectionHeader title="Kimliğimiz" />
          <View style={{ height: space.xs }} />
          {identity.map(([title, text, [from, to]], i) => (
            <FadeIn key={title} index={2 + i} style={styles.identity}>
              <Gradient vertical={false} stops={[{ offset: 0, color: from }, { offset: 1, color: to }]} />
              <Text style={styles.identityTitle}>{title.replace(/\n/g, ' ')}</Text>
              {text ? <Text style={styles.identityText}>{text}</Text> : null}
            </FadeIn>
          ))}
        </View>
      ) : null}

      {points.data?.length ? (
        <View style={{ gap: space.md }}>
          <SectionHeader title="Bilet noktaları" />
          <View style={{ height: space.xs }} />
          {points.data.map((p, i) => (
            <FadeIn key={p.id} index={i} style={styles.card}>
              <View style={styles.pointHead}>
                <Image source={{ uri: p.logo }} style={styles.pointLogo} />
                <Text style={[T.h3, { flex: 1 }]}>{p.name}</Text>
                <Icon name="ticket" size={22} color={c.gold} />
              </View>
              {p.address ? <Row icon="pin" text={p.address} /> : null}
              {p.working_hours ? <Row icon="clock" text={p.working_hours} /> : null}
              <View style={styles.pointActions}>
                {p.maps_url ? <Button title="Yol tarifi" icon="pin" variant="secondary" onPress={() => openUrl(p.maps_url)} style={{ flex: 1 }} /> : null}
                {p.phone ? <Button title="Ara" icon="phone" variant="ghost" onPress={() => openUrl(`tel:${p.phone!.replace(/\s/g, '')}`)} style={{ flex: 1 }} /> : null}
              </View>
            </FadeIn>
          ))}
        </View>
      ) : null}

      {sponsors.data?.length ? (
        <View style={{ gap: space.md }}>
          <SectionHeader title="Sponsorlarımız" />
          <View style={{ height: space.xs }} />
          <View style={styles.sponsors}>
            {sponsors.data.map(s => (
              <PressableScale
                key={s.id}
                onPress={s.website ? () => openUrl(s.website) : undefined}
                disabled={!s.website}
                accessibilityRole={s.website ? 'link' : 'image'}
                accessibilityLabel={`${s.name}, ${s.category}`}
                style={styles.sponsor}>
                <Image source={{ uri: s.logo }} style={styles.sponsorLogo} resizeMode="contain" />
              </PressableScale>
            ))}
          </View>
        </View>
      ) : null}

      <Appearance />

      <Newsletter />

      {info.footer_copyright ? <Text style={[T.small, { textAlign: 'center' }]}>© {new Date().getFullYear()} {info.footer_copyright}</Text> : null}
    </ParallaxScreen>
  );
}

function Row({ icon, text }: { icon: IconName; text: string }) {
  const { c, T } = useTheme();
  const styles = useStyles();
  return (
    <View style={styles.row}>
      <Icon name={icon} size={16} color={c.muted} />
      <Text style={[T.body, { flex: 1, fontSize: 14 }]}>{text}</Text>
    </View>
  );
}

/** Görünüm: telefonun ayarını izle ya da açık/koyu temayı elle seç */
function Appearance() {
  const { c, T, pref, setPref } = useTheme();
  const styles = useStyles();
  return (
    <View style={styles.card} testID="appearance">
      <View style={styles.row}><Icon name={c.mode === 'dark' ? 'moon' : 'sun'} size={22} color={c.gold} /><Text style={T.h2}>Görünüm</Text></View>
      <Segmented<ThemePref>
        value={pref}
        onChange={setPref}
        options={[{ value: 'system', label: 'Sistem' }, { value: 'light', label: 'Açık' }, { value: 'dark', label: 'Koyu' }]}
      />
      <Text style={T.small}>"Sistem" seçiliyken uygulama telefonunun açık/koyu ayarını izler.</Text>
    </View>
  );
}

function Newsletter() {
  const { c, T } = useTheme();
  const styles = useStyles();
  const [email, setEmail] = useState('');
  const sub = useSubscribe();
  const error = sub.error instanceof ApiError ? sub.error.message : sub.error ? 'Bir sorun oluştu.' : null;
  return (
    <View style={[styles.card, { borderColor: 'rgba(229,169,60,0.35)', borderWidth: 1 }]}>
      <View style={styles.row}><Icon name="mail" size={22} color={c.gold} /><Text style={T.h2}>E-bülten</Text></View>
      <Text style={T.body}>Maç duyuruları ve kulüp haberleri e-postana gelsin.</Text>
      {sub.data ? <Text style={styles.ok}>{sub.data.message}</Text> : null}
      {error ? <Text style={styles.err}>{error}</Text> : null}
      <TextInput
        value={email}
        onChangeText={setEmail}
        placeholder="ornek@eposta.com"
        placeholderTextColor={c.faint}
        keyboardType="email-address"
        autoCapitalize="none"
        autoComplete="email"
        selectionColor={c.gold}
        style={styles.email}
        accessibilityLabel="E-posta adresin"
      />
      <Button title="Kaydol" variant="gold" onPress={() => sub.mutate(email.trim())} loading={sub.isPending} disabled={!email.trim()} />
    </View>
  );
}

const useStyles = createStyles((c) => StyleSheet.create({
  hero: { alignItems: 'center' },
  logo: { width: 92, height: 92, borderRadius: 46, borderWidth: 3, borderColor: c.white, marginBottom: space.sm },
  city: { fontFamily: fonts.condBold, fontSize: 15, letterSpacing: 4, color: 'rgba(255,255,255,0.8)' },
  name: { fontFamily: fonts.condBlackItalic, fontSize: 48, lineHeight: 48, color: c.white },
  hashtag: { fontFamily: fonts.condBold, fontSize: 15, letterSpacing: 1, color: c.gold },
  motto: { fontFamily: fonts.condBlack, fontSize: 26, lineHeight: 28, color: c.text, textAlign: 'center', textTransform: 'uppercase', marginTop: space.sm },
  socials: { flexDirection: 'row', flexWrap: 'wrap', gap: space.sm },
  social: { flexGrow: 1, flexBasis: '45%', flexDirection: 'row', alignItems: 'center', gap: space.sm, backgroundColor: c.surface, borderWidth: StyleSheet.hairlineWidth, borderColor: c.lineStrong, borderRadius: radius.md, padding: space.md, minHeight: 52 },
  socialText: { fontFamily: fonts.condBold, fontSize: 16, letterSpacing: 0.8, color: c.text, textTransform: 'uppercase' },
  identity: { borderRadius: radius.xl, padding: space.xl, gap: 8, overflow: 'hidden' },
  identityTitle: { fontFamily: fonts.condBlack, fontSize: 28, lineHeight: 29, color: c.white, textTransform: 'uppercase' },
  identityText: { fontFamily: fonts.regular, fontSize: 15, lineHeight: 22, color: 'rgba(255,255,255,0.88)' },
  card: { backgroundColor: c.surface, borderRadius: radius.xl, padding: space.lg, gap: space.md, borderWidth: StyleSheet.hairlineWidth, borderColor: c.line },
  pointHead: { flexDirection: 'row', alignItems: 'center', gap: space.md },
  pointLogo: { width: 46, height: 46, borderRadius: radius.md, backgroundColor: c.white },
  pointActions: { flexDirection: 'row', gap: space.sm },
  row: { flexDirection: 'row', alignItems: 'center', gap: space.sm },
  sponsors: { flexDirection: 'row', flexWrap: 'wrap', gap: space.sm },
  sponsor: { width: '31.5%', aspectRatio: 1.5, backgroundColor: c.white, borderRadius: radius.md, padding: space.sm, alignItems: 'center', justifyContent: 'center', borderWidth: StyleSheet.hairlineWidth, borderColor: c.lineStrong },
  sponsorLogo: { width: '100%', height: '100%' },
  email: { borderWidth: 1.5, borderColor: c.lineStrong, backgroundColor: c.surface2, borderRadius: radius.md, height: 52, paddingHorizontal: space.md, fontFamily: fonts.medium, fontSize: 16, color: c.text },
  ok: { fontFamily: fonts.semibold, color: c.greenGlow },
  err: { fontFamily: fonts.semibold, color: c.mode === 'dark' ? '#FF8A8A' : c.loss },
}));
