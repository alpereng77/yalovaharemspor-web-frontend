import React, { useState } from 'react';
import { Image, ImageLoadEvent, StyleSheet, View } from 'react-native';

/**
 * Üstten hizalı fotoğraf: kabı genişlik boyunca doldurur, kendi oranını korur ve ÜSTE yaslanır;
 * taşan kısım alttan kesilir. Oyuncu fotoğraflarında (TBF: dar ve uzun, ~0,44) baş kesilmez.
 * React Native'de "object-position: top" olmadığı için oran, fotoğraf yüklenince okunur.
 */
export function TopImage({ uri, fallbackRatio = 0.44, accessibilityLabel }: { uri: string; fallbackRatio?: number; accessibilityLabel?: string }) {
  const [ratio, setRatio] = useState(fallbackRatio);
  const onLoad = (e: ImageLoadEvent) => {
    const { width, height } = e.nativeEvent.source ?? {};
    if (width && height) {
      setRatio(width / height);
    }
  };
  return (
    <View style={StyleSheet.absoluteFill} pointerEvents="none">
      <Image
        source={{ uri }}
        onLoad={onLoad}
        resizeMode="cover"
        accessibilityLabel={accessibilityLabel}
        // Genişlik %100, yükseklik orana göre; kabın en az tamamını kaplar
        style={[styles.img, { aspectRatio: ratio }]}
      />
    </View>
  );
}

const styles = StyleSheet.create({
  img: { position: 'absolute', top: 0, left: 0, width: '100%', minHeight: '100%' },
});
