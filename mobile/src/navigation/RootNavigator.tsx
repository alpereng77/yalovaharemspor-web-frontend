import React, { useMemo } from 'react';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { DarkTheme, DefaultTheme, NavigationContainer, Theme } from '@react-navigation/native';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { ArticleScreen } from '../screens/ArticleScreen';
import { ClubScreen } from '../screens/ClubScreen';
import { GiveawayScreen } from '../screens/GiveawayScreen';
import { GiveawaysScreen } from '../screens/GiveawaysScreen';
import { HomeScreen } from '../screens/HomeScreen';
import { MatchesScreen } from '../screens/MatchesScreen';
import { NewsScreen } from '../screens/NewsScreen';
import { PlayerScreen } from '../screens/PlayerScreen';
import { SquadScreen } from '../screens/SquadScreen';
import { fonts } from '../theme';
import { useTheme } from '../themeContext';
import { BackButton } from './BackButton';
import { TabBar } from './TabBar';
import type { RootStackParamList, TabParamList } from './types';

const Stack = createNativeStackNavigator<RootStackParamList>();
const Tab = createBottomTabNavigator<TabParamList>();

/** Gezinme kabuğu renkleri seçili paletten üretilir */
function useNavTheme(): Theme {
  const { c } = useTheme();
  const base = c.mode === 'dark' ? DarkTheme : DefaultTheme;
  return useMemo(() => ({
    ...base,
    colors: { ...base.colors, primary: c.red, background: c.bg, card: c.bg, text: c.text, border: c.line },
  }), [base, c]);
}

const titleStyle = (color: string) => ({ fontFamily: fonts.condBlack, fontSize: 22, color });

function Tabs() {
  const { c } = useTheme();
  return (
    <Tab.Navigator
      tabBar={props => <TabBar {...props} />}
      screenOptions={{
        headerStyle: { backgroundColor: c.bg },
        headerTitleStyle: titleStyle(c.text),
        headerShadowVisible: false,
        headerTitleAlign: 'left',
        animation: 'shift',
      }}>
      <Tab.Screen name="Home" component={HomeScreen} options={{ title: 'Ana Sayfa', headerShown: false }} />
      <Tab.Screen name="Matches" component={MatchesScreen} options={{ title: 'Maçlar', headerTitle: 'MAÇ MERKEZİ' }} />
      <Tab.Screen name="Squad" component={SquadScreen} options={{ title: 'Kadro', headerTitle: 'KADRO' }} />
      <Tab.Screen name="News" component={NewsScreen} options={{ title: 'Haberler', headerTitle: 'HABERLER' }} />
      <Tab.Screen name="Giveaways" component={GiveawaysScreen} options={{ title: 'Çekiliş', headerTitle: 'ÇEKİLİŞLER' }} />
    </Tab.Navigator>
  );
}

/** Detay ekranları: başlık çubuğu fotoğrafın üzerinde şeffaf, sağdan kayarak açılır */
const overlayHeader = {
  headerTransparent: true,
  headerTitle: '',
  headerBackVisible: false,
  headerLeft: () => <BackButton />,
};

export function RootNavigator() {
  const { c } = useTheme();
  const theme = useNavTheme();
  return (
    <NavigationContainer theme={theme}>
      <Stack.Navigator
        screenOptions={{
          headerTintColor: c.text,
          headerTitleStyle: titleStyle(c.text),
          headerStyle: { backgroundColor: c.bg },
          headerShadowVisible: false,
          headerBackButtonDisplayMode: 'minimal',
          contentStyle: { backgroundColor: c.bg },
          animation: 'slide_from_right',
        }}>
        <Stack.Screen name="Tabs" component={Tabs} options={{ headerShown: false }} />
        <Stack.Screen name="Player" component={PlayerScreen} options={overlayHeader} />
        <Stack.Screen name="Article" component={ArticleScreen} options={overlayHeader} />
        <Stack.Screen name="Giveaway" component={GiveawayScreen} options={overlayHeader} />
        <Stack.Screen name="Club" component={ClubScreen} options={overlayHeader} />
      </Stack.Navigator>
    </NavigationContainer>
  );
}
