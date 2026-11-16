import React from 'react';
import { StyleSheet } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { Icon } from '../components/Icon';
import { PressableScale } from '../components/motion';

/**
 * Fotoğraflı detay ekranlarının geri butonu: yarı saydam koyu daire içinde beyaz ok.
 * Fotoğrafın üstünde de, kaydırınca açık zeminde de her iki temada okunur.
 */
export function BackButton() {
  const navigation = useNavigation();
  return (
    <PressableScale
      onPress={() => navigation.canGoBack() && navigation.goBack()}
      hitSlop={10}
      accessibilityLabel="Geri"
      style={styles.btn}
      testID="back-button">
      <Icon name="back" size={22} color="#FFFFFF" strokeWidth={2.6} />
    </PressableScale>
  );
}

const styles = StyleSheet.create({
  btn: {
    width: 40, height: 40, borderRadius: 20, alignItems: 'center', justifyContent: 'center',
    backgroundColor: 'rgba(5,16,10,0.55)', borderWidth: StyleSheet.hairlineWidth, borderColor: 'rgba(255,255,255,0.25)',
  },
});
