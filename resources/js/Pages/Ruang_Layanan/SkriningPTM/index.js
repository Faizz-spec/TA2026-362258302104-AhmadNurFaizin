// Export semua komponen dari components directory
export { default as SkriningHeader } from './components/SkriningHeader.vue';
export { default as SkriningNav } from './components/SkriningNav.vue';
export { default as NavItem } from './components/NavItem.vue';
export { default as StepPanel } from './components/StepPanel.vue';
export { default as CardSection } from './components/CardSection.vue';
export { default as FormGrid } from './components/FormGrid.vue';
export { default as FormField } from './components/FormField.vue';
export { default as AlertBox } from './components/AlertBox.vue';
export { default as RadioGroup } from './components/RadioGroup.vue';
export { default as InputGroup } from './components/InputGroup.vue';
export { default as NavButtons } from './components/NavButtons.vue';

// Export komponen utama
export { default as SkriningPTM } from './SkriningPTM.vue';

// Utility composable untuk perhitungan
export const useCalculations = () => {
  const hitungIMT = (bb, tb) => {
    if (bb > 0 && tb > 0) {
      return bb / (tb / 100) ** 2;
    }
    return 0;
  };

  const getIMTCategory = (imt) => {
    if (imt < 18.5) return '🔵 Kurus';
    else if (imt < 23) return '🟢 Normal';
    else if (imt < 27) return '🟡 Gemuk';
    else return '🔴 Obesitas';
  };

  const interpretTD = (sistolik, diastolik) => {
    if (sistolik < 120 && diastolik < 80) return '🟢 Normal';
    else if (sistolik < 130 && diastolik < 80) return '🟡 Elevated';
    else if (sistolik < 140 || diastolik < 90) return '🟠 Hipertensi Grade 1';
    else if (sistolik < 180 || diastolik < 110) return '🔴 Hipertensi Grade 2';
    else return '⛔ Krisis Hipertensi';
  };

  const interpretLP = (lp, jk) => {
    const batas = jk === 'female' ? 80 : 90;
    return lp >= batas ? `🔴 Berisiko (≥${batas} cm)` : `🟢 Normal (<${batas} cm)`;
  };

  const interpretGDS = (gds) => {
    if (gds < 140) return '🟢 Normal';
    else if (gds < 200) return '🟡 Pra-DM';
    else return '🔴 DM';
  };

  const interpretGDP = (gdp) => {
    if (gdp < 100) return '🟢 Normal';
    else if (gdp < 126) return '🟡 Pra-DM';
    else return '🔴 DM';
  };

  const interpretHbA1c = (hba1c) => {
    if (hba1c < 5.7) return '🟢 Normal';
    else if (hba1c < 6.5) return '🟡 Pra-DM';
    else return '🔴 DM';
  };

  const interpretKolesterol = (koltot) => {
    if (koltot < 200) return '🟢 Normal';
    else if (koltot < 240) return '🟡 Borderline';
    else return '🔴 Tinggi';
  };

  const interpretLDL = (ldl) => {
    if (ldl < 100) return '🟢 Optimal';
    else if (ldl < 130) return '🟡 Near Optimal';
    else if (ldl < 160) return '🟠 Borderline';
    else return '🔴 Tinggi';
  };

  const interpretTG = (tg) => {
    if (tg < 150) return '🟢 Normal';
    else if (tg < 200) return '🟡 Borderline';
    else if (tg < 500) return '🔴 Tinggi';
    else return '⛔ Sangat Tinggi';
  };

  const interpretAsamUrat = (val, jk) => {
    const batas = jk === 'female' ? 6 : 7;
    return val > batas ? `🔴 Hiperurisemia (>${batas} mg/dL)` : `🟢 Normal (≤${batas} mg/dL)`;
  };

  return {
    hitungIMT,
    getIMTCategory,
    interpretTD,
    interpretLP,
    interpretGDS,
    interpretGDP,
    interpretHbA1c,
    interpretKolesterol,
    interpretLDL,
    interpretTG,
    interpretAsamUrat,
  };
};
