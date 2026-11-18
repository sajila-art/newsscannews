import type { Metadata } from 'next';
import { Playfair_Display, Inter, Anek_Malayalam } from 'next/font/google';
import './globals.css';
import LayoutClientWrapper from './LayoutClientWrapper';

const playfair = Playfair_Display({
  subsets: ['latin'],
  variable: '--font-serif',
  display: 'swap',
});

const inter = Inter({
  subsets: ['latin'],
  variable: '--font-sans',
  display: 'swap',
});

const malayalam = Anek_Malayalam({
  subsets: ['malayalam'],
  variable: '--font-malayalam',
  weight: ['300', '400', '500', '600', '700', '800'],
  display: 'swap',
});

export const metadata: Metadata = {
  title: 'എൻ-ന്യൂസ് വാർത്തകൾ | N-NEWS & Scan Paper',
  description: 'പ്രാദേശിക വാർത്തകൾ, ജില്ലാ വാർത്തകൾ, സംസ്ഥാന വാർത്തകൾ, ക്രൈം ഫയൽ, ബിസിനസ്, GenZ കോളം, സ്കൂൾ കോർണർ, അറിയിപ്പുകൾ, ജോലി ഒഴിവുകൾ, എഴുത്തുപുര, വിജയവഴിയിൽ, ലേഡീസ് കോർണർ.',
  keywords: ['Malayalam news', 'Kerala e-paper', 'സ്കാൻ ന്യൂസ്', 'ഇ-പേപ്പർ', 'N-NEWS', 'പ്രാദേശിക വാർത്തകൾ'],
  icons: {
    icon: '/logo.jpg',
    apple: '/logo.jpg',
  },
};

export default function RootLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <html
      lang="ml"
      className={`${playfair.variable} ${inter.variable} ${malayalam.variable}`}
    >
      <body className="min-h-screen flex flex-col bg-stone-50 dark:bg-stone-950 text-stone-900 dark:text-stone-100 font-sans">
        <LayoutClientWrapper>{children}</LayoutClientWrapper>
      </body>
    </html>
  );
}
