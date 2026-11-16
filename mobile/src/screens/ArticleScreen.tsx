import React from 'react';
import { StyleSheet, Text, View } from 'react-native';
import { useArticle } from '../api/hooks';
import { FadeIn } from '../components/motion';
import { ParallaxScreen } from '../components/Parallax';
import { Button, ErrorState, Loading, openUrl } from '../components/ui';
import { fullDate } from '../lib/format';
import type { RootScreenProps } from '../navigation/types';
import { fonts, space } from '../theme';
import { createStyles } from '../themeContext';

export function ArticleScreen({ route }: RootScreenProps<'Article'>) {
  const styles = useStyles();
  const { data: a, isPending, error, refetch } = useArticle(route.params.id);
  if (isPending) {
    return <Loading variant="detail" />;
  }
  if (!a) {
    return <ErrorState error={error} onRetry={refetch} />;
  }
  const paragraphs = (a.content || a.summary || '').split(/\n{2,}|\r\n\r\n/).map(t => t.trim()).filter(Boolean);
  const kicker = a.type === 'press' ? `BASINDA${a.source_title ? ' · ' + a.source_title : ''}` : a.category;

  return (
    <ParallaxScreen
      image={{ uri: a.image }}
      height={360}
      onRefresh={refetch}
      testID="article-screen"
      overlay={
        <View style={{ gap: 8 }}>
          <View style={styles.kickerRow}>
            <View style={styles.kickerBar} />
            <Text style={styles.kicker}>{kicker}</Text>
          </View>
          <Text style={styles.title} accessibilityRole="header">{a.title}</Text>
        </View>
      }>
      {a.date ? <Text style={styles.date}>{fullDate(a.date)}</Text> : null}
      <FadeIn style={{ gap: space.lg }}>
        {paragraphs.map((p, i) => (
          <Text key={i} style={[styles.paragraph, i === 0 && styles.lead]} selectable>{p}</Text>
        ))}
      </FadeIn>
      {a.source_url ? (
        <Button title={a.type === 'press' ? 'Haberin tamamı' : 'Kaynağa git'} icon="external" variant="secondary" onPress={() => openUrl(a.source_url)} />
      ) : null}
    </ParallaxScreen>
  );
}

const useStyles = createStyles((c) => StyleSheet.create({
  kickerRow: { flexDirection: 'row', alignItems: 'center', gap: 8 },
  kickerBar: { width: 22, height: 3, backgroundColor: c.red },
  kicker: { fontFamily: fonts.condBold, fontSize: 13, letterSpacing: 2, color: c.gold, textTransform: 'uppercase' },
  title: { fontFamily: fonts.condBlack, fontSize: 36, lineHeight: 37, color: c.white, textTransform: 'uppercase' },
  date: { fontFamily: fonts.condBold, fontSize: 13, letterSpacing: 1.5, color: c.muted, textTransform: 'uppercase', marginTop: space.sm },
  paragraph: { fontFamily: fonts.regular, fontSize: 17, lineHeight: 28, color: c.textSoft },
  lead: { fontFamily: fonts.medium, fontSize: 19, lineHeight: 29, color: c.text },
}));
