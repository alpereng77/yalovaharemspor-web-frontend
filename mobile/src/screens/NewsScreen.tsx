import React, { useState } from 'react';
import { View } from 'react-native';
import { useNews } from '../api/hooks';
import { NewsFeature, NewsRow } from '../components/cards';
import { FadeIn } from '../components/motion';
import { Empty, ErrorState, Loading, Screen, Segmented } from '../components/ui';
import type { TabScreenProps } from '../navigation/types';
import { space } from '../theme';
import { useTheme } from '../themeContext';

type Filter = 'all' | 'blog' | 'press';

export function NewsScreen({ navigation }: TabScreenProps<'News'>) {
  const { c } = useTheme();
  const [filter, setFilter] = useState<Filter>('all');
  const { data, isPending, error, refetch } = useNews(filter);
  const open = (id: number, title: string) => navigation.navigate('Article', { id, title });

  return (
    <Screen onRefresh={refetch} testID="news-screen">
      <Segmented<Filter>
        value={filter}
        onChange={setFilter}
        options={[{ value: 'all', label: 'Hepsi' }, { value: 'blog', label: 'Kulüpten' }, { value: 'press', label: 'Basında' }]}
      />
      {isPending ? <Loading /> : error && !data ? <ErrorState error={error} onRetry={refetch} />
        : !data?.length ? <Empty icon="news" title="Henüz haber yok" />
        : (
          <>
            <FadeIn><NewsFeature article={data[0]} onPress={() => open(data[0].id, data[0].title)} /></FadeIn>
            <View style={{ gap: space.lg }}>
              {data.slice(1).map((a, i) => (
                <FadeIn key={a.id} index={i + 1}>
                  <NewsRow article={a} onPress={() => open(a.id, a.title)} />
                  <View style={{ height: 1, backgroundColor: c.line, marginTop: space.lg }} />
                </FadeIn>
              ))}
            </View>
          </>
        )}
    </Screen>
  );
}
