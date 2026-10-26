// Konfigurasi Tema Warna Berdasarkan Pilihan Avatar
export const AVATAR_THEMES = [
  {
    id: 'budi',
    label: 'Budi',
    role: 'Food Hunter',
    url: 'https://api.dicebear.com/7.x/adventurer/svg?seed=Budi',
    themeName: 'Oranye Nitip (Sunset)',
    primary: '#f97316',
    primaryHover: '#ea580c',
    secondary: '#f59e0b',
    lightBg: 'rgba(249, 115, 22, 0.09)',
    border: 'rgba(249, 115, 22, 0.28)',
    shadow: 'rgba(249, 115, 22, 0.35)',
    darkBg: 'rgba(249, 115, 22, 0.18)',
    darkText: '#fb923c',
    gradient: 'linear-gradient(to right, #f97316, #f59e0b)',
    themeClass: 'theme-budi'
  },
  {
    id: 'fathan',
    label: 'Fathan',
    role: 'Tech Lead',
    url: 'https://api.dicebear.com/7.x/adventurer/svg?seed=Fathan',
    themeName: 'Biru Neon (Ocean)',
    primary: '#2563eb',
    primaryHover: '#1d4ed8',
    secondary: '#06b6d4',
    lightBg: 'rgba(37, 99, 235, 0.09)',
    border: 'rgba(37, 99, 235, 0.28)',
    shadow: 'rgba(37, 99, 235, 0.35)',
    darkBg: 'rgba(37, 99, 235, 0.18)',
    darkText: '#60a5fa',
    gradient: 'linear-gradient(to right, #2563eb, #06b6d4)',
    themeClass: 'theme-fathan'
  },
  {
    id: 'radiz',
    label: 'Radiz',
    role: 'Creative Mind',
    url: 'https://api.dicebear.com/7.x/adventurer/svg?seed=Radiz',
    themeName: 'Ungu Kreatif (Cyber)',
    primary: '#8b5cf6',
    primaryHover: '#7c3aed',
    secondary: '#d946ef',
    lightBg: 'rgba(139, 92, 246, 0.09)',
    border: 'rgba(139, 92, 246, 0.28)',
    shadow: 'rgba(139, 92, 246, 0.35)',
    darkBg: 'rgba(139, 92, 246, 0.18)',
    darkText: '#a78bfa',
    gradient: 'linear-gradient(to right, #8b5cf6, #d946ef)',
    themeClass: 'theme-radiz'
  },
  {
    id: 'putri',
    label: 'Putri',
    role: 'Sirkel Star',
    url: 'https://api.dicebear.com/7.x/adventurer/svg?seed=Putri',
    themeName: 'Merah Rose (Berry)',
    primary: '#f43f5e',
    primaryHover: '#e11d48',
    secondary: '#ec4899',
    lightBg: 'rgba(244, 63, 94, 0.09)',
    border: 'rgba(244, 63, 94, 0.28)',
    shadow: 'rgba(244, 63, 94, 0.35)',
    darkBg: 'rgba(244, 63, 94, 0.18)',
    darkText: '#fb7185',
    gradient: 'linear-gradient(to right, #f43f5e, #ec4899)',
    themeClass: 'theme-putri'
  },
  {
    id: 'siti',
    label: 'Siti',
    role: 'Healthy Foodie',
    url: 'https://api.dicebear.com/7.x/adventurer/svg?seed=Siti',
    themeName: 'Hijau Segar (Mint)',
    primary: '#10b981',
    primaryHover: '#059669',
    secondary: '#14b8a6',
    lightBg: 'rgba(16, 185, 129, 0.09)',
    border: 'rgba(16, 185, 129, 0.28)',
    shadow: 'rgba(16, 185, 129, 0.35)',
    darkBg: 'rgba(16, 185, 129, 0.18)',
    darkText: '#34d399',
    gradient: 'linear-gradient(to right, #10b981, #14b8a6)',
    themeClass: 'theme-siti'
  },
  {
    id: 'dimas',
    label: 'Dimas',
    role: 'Master Chef',
    url: 'https://api.dicebear.com/7.x/adventurer/svg?seed=Dimas',
    themeName: 'Kuning Emas (Golden)',
    primary: '#d97706',
    primaryHover: '#b45309',
    secondary: '#eab308',
    lightBg: 'rgba(217, 119, 6, 0.09)',
    border: 'rgba(217, 119, 6, 0.28)',
    shadow: 'rgba(217, 119, 6, 0.35)',
    darkBg: 'rgba(217, 119, 6, 0.18)',
    darkText: '#fbbf24',
    gradient: 'linear-gradient(to right, #d97706, #eab308)',
    themeClass: 'theme-dimas'
  }
]

export const getThemeByAvatarUrl = (url) => {
  if (!url) return AVATAR_THEMES[0]
  const match = AVATAR_THEMES.find(t => t.url === url || (typeof url === 'string' && url.includes(t.label)))
  return match || AVATAR_THEMES[0]
}

export const getThemeById = (id) => {
  return AVATAR_THEMES.find(t => t.id === id) || AVATAR_THEMES[0]
}
