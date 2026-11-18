# 📰 The N-NEWS & Scan News Portal (മലയാളം എൻ-ന്യൂസ് & ഇ-പേപ്പർ)

A modern, high-performance Next.js 14 frontend starter built for digital news publications and interactive scanned newspaper readers (**ഇ-പേപ്പർ / Scan News**).

Built with **Next.js 14 (App Router)**, **TypeScript**, **Tailwind CSS**, **Anek Malayalam (Google Fonts)**, and **Lucide Icons**.

---

## 📑 ഉൾപ്പെടുത്തിയിരിക്കുന്ന പ്രധാന വാർത്താ വിഭാഗങ്ങൾ (12 Categories)

1. **പ്രാദേശിക വാർത്തകൾ** (`/categories/local`) — ഗ്രാമ-പഞ്ചായത്ത് പ്രാദേശിക വാർത്തകൾ
2. **ജില്ലാ വാർത്തകൾ** (`/categories/district`) — ജില്ലാതല പ്രധാന സംഭവങ്ങൾ
3. **സംസ്ഥാന വാർത്തകൾ** (`/categories/state`) — കേരള സംസ്ഥാന വാർത്തകൾ
4. **ക്രൈം ഫയൽ** (`/categories/crime`) — അന്വേഷണ റിപ്പോർട്ടുകൾ, പോലീസ് & കോടതി വാർത്തകൾ
5. **ബിസിനസ് വാർത്തകൾ** (`/categories/business`) — വിപണി, സ്വർണ്ണവില, വാണിജ്യ വിശേഷങ്ങൾ
6. **Genz കോളം** (`/categories/genz`) — പുതുതലമുറ ട്രെൻഡുകൾ, സോഷ്യൽ മീഡിയ & ടെക്നോളജി
7. **സ്കൂൾ കോർണർ** (`/categories/school`) — വിദ്യാഭ്യാസം, പരീക്ഷാ തീയതികൾ, ക്യാമ്പസ് വിശേഷങ്ങൾ
8. **അറിയിപ്പുകൾ** (`/categories/notices`) — സർക്കാർ വിജ്ഞാപനങ്ങൾ, പൊതു അറിയിപ്പുകൾ
9. **ജോലി ഒഴിവുകൾ** (`/categories/jobs`) — പി.എസ്.സി, ബാങ്കിംഗ്, ഐ.ടി തൊഴിലവസരങ്ങൾ
10. **എഴുത്തുപുര** (`/categories/literature`) — കഥ, കവിത, സാംസ്കാരിക പംക്തികൾ
11. **വിജയവഴിയിൽ** (`/categories/success`) — പ്രചോദനാത്മക അനുഭവങ്ങൾ, സംരംഭക വിജയങ്ങൾ
12. **ലേഡീസ് കോർണർ** (`/categories/ladies`) — ആരോഗ്യം, ഫാഷൻ, പാചകം, വനിതാ സംരംഭങ്ങൾ

---

## ✨ പ്രധാന സവിശേഷതകൾ (Key Features)

* **🗞️ ഇ-പേപ്പർ സ്കാൻ റീഡർ (`/scan-news`)**: 6 സമ്പൂർണ്ണ അച്ചടി പേജുകൾ, സൂം & പാൻ, വാർത്താ ബോക്സുകളിൽ ക്ലിക്ക് ചെയ്ത് വായിക്കാവുന്ന സ്മാർട്ട് ക്ലിപ്പിംഗ് (OCR).
* **🔊 ടെക്സ്റ്റ്-ടു-സ്പീച്ച് ഓഡിയോ റീഡർ**: ഏത് വാർത്തയും മലയാളത്തിൽ കേൾക്കാനുള്ള വോയ്സ് റീഡർ.
* **⚡ തത്സമയ ബ്രേക്കിംഗ് ന്യൂസ് ടിക്കർ**: ലൈവ് സ്ക്രോളിംഗ് വാർത്താ വിവരങ്ങൾ.
* **🔍 ഇൻസ്റ്റന്റ് സേർച്ച് (`⌘K` / `Ctrl+K`)**: വാർത്തകളും ഇ-പേപ്പർ പേജുകളും വേഗത്തിൽ തിരയാം.
* **📈 തത്സമയ വിപണി നിരക്കുകൾ**: സെൻസെക്സ്, നിഫ്റ്റി, സ്വർണ്ണവില, ഇന്ധനവില.
* **🌓 ഡാർക്ക് & ലൈറ്റ് മോഡ്**: സുഖകരമായ വായനാനുഭവം.

---

## 🚀 എങ്ങനെ റൺ ചെയ്യാം (How to Run)

```powershell
# 1. പാക്കേജുകൾ ഇൻസ്റ്റാൾ ചെയ്യുക
npm install

# 2. ഡെവലപ്‌മെന്റ് സെർവർ ആരംഭിക്കുക
npm run dev
```

തുടർന്ന് ബ്രൗസറിൽ **`http://localhost:3000`** സന്ദർശിക്കുക.

## Backend (CodeIgniter 3)

The MySQL-backed JSON API is served by the CodeIgniter backend. For a local WAMP setup, set `NEXT_PUBLIC_API_URL=http://localhost/e_news/backend` in `.env.local` and restart the Next.js development server. Category pages then load published articles from the database using each category's ID. Without the API URL, the frontend uses its bundled mock data.
