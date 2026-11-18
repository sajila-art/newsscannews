import { NextRequest, NextResponse } from 'next/server';
import { MARKET_INDICES } from '../../../data/mockData';
import { MarketIndex } from '../../../types/news';

interface EditionGeo {
  cityMl: string;
  lat: number;
  lon: number;
  fallbackTemp: number;
}

const EDITION_GEO: Record<string, EditionGeo> = {
  'kerala-central': { cityMl: 'കൊച്ചി', lat: 9.9312, lon: 76.2673, fallbackTemp: 28 },
  'trivandrum-south': { cityMl: 'തിരുവനന്തപുരം', lat: 8.5241, lon: 76.9366, fallbackTemp: 29 },
  'calicut-malabar': { cityMl: 'കോഴിക്കോട്', lat: 11.2588, lon: 75.7804, fallbackTemp: 28 },
  'gulf-edition': { cityMl: 'ദുബായ്', lat: 25.2048, lon: 55.2708, fallbackTemp: 31 },
};

const CACHE_TTL_MS = 5 * 60 * 1000;
const weatherCache = new Map<string, { expiresAt: number; data: WeatherPayload }>();
let marketCache: { expiresAt: number; data: MarketIndex[] } | null = null;

interface WeatherPayload {
  cityMl: string;
  tempC: number;
  aqi: number | null;
  aqiLabelMl: string;
}

function getAqiLabelMl(aqi: number | null): string {
  if (aqi === null || Number.isNaN(aqi)) return 'നല്ലത്';
  if (aqi <= 50) return 'നല്ലത്';
  if (aqi <= 100) return 'മിതമായത്';
  if (aqi <= 150) return 'ശരാശരി';
  return 'മോശം';
}

function formatNumber(n: number, decimals = 2): string {
  return n.toLocaleString('en-IN', {
    minimumFractionDigits: decimals,
    maximumFractionDigits: decimals,
  });
}

function formatSigned(n: number, decimals = 2): { text: string; isPositive: boolean } {
  const isPositive = n >= 0;
  const sign = isPositive ? '+' : '-';
  return {
    text: `${sign}${formatNumber(Math.abs(n), decimals)}`,
    isPositive,
  };
}

async function fetchWeather(edition: string, customLat?: number, customLon?: number, customCity?: string): Promise<WeatherPayload> {
  const base = EDITION_GEO[edition] || EDITION_GEO['kerala-central'];
  const lat = customLat !== undefined ? customLat : base.lat;
  const lon = customLon !== undefined ? customLon : base.lon;
  const cityMl = customCity || base.cityMl;
  const cacheKey = `${lat.toFixed(2)},${lon.toFixed(2)},${cityMl}`;

  const cached = weatherCache.get(cacheKey);
  if (cached && cached.expiresAt > Date.now()) {
    return cached.data;
  }

  let tempC = base.fallbackTemp;
  let aqi: number | null = 42;

  try {
    const [weatherRes, aqiRes] = await Promise.allSettled([
      fetch(
        `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current=temperature_2m`,
        { signal: AbortSignal.timeout(4500), cache: 'no-store' }
      ).then((r) => (r.ok ? r.json() : null)),
      fetch(
        `https://air-quality-api.open-meteo.com/v1/air-quality?latitude=${lat}&longitude=${lon}&current=us_aqi`,
        { signal: AbortSignal.timeout(4500), cache: 'no-store' }
      ).then((r) => (r.ok ? r.json() : null)),
    ]);

    if (weatherRes.status === 'fulfilled' && typeof weatherRes.value?.current?.temperature_2m === 'number') {
      tempC = Math.round(weatherRes.value.current.temperature_2m);
    }
    if (aqiRes.status === 'fulfilled' && typeof aqiRes.value?.current?.us_aqi === 'number') {
      aqi = Math.round(aqiRes.value.current.us_aqi);
    }
  } catch {
    // Keep fallback weather
  }

  const payload: WeatherPayload = {
    cityMl,
    tempC,
    aqi,
    aqiLabelMl: getAqiLabelMl(aqi),
  };

  weatherCache.set(cacheKey, { expiresAt: Date.now() + CACHE_TTL_MS, data: payload });
  return payload;
}

async function fetchYahooIndex(symbol: string, displayName: string): Promise<MarketIndex | null> {
  try {
    const res = await fetch(
      `https://query1.finance.yahoo.com/v8/finance/chart/${encodeURIComponent(symbol)}?interval=1d&range=1d`,
      {
        headers: { 'User-Agent': 'Mozilla/5.0' },
        signal: AbortSignal.timeout(4500),
        cache: 'no-store',
      }
    );
    if (!res.ok) return null;
    const json = await res.json();
    const meta = json?.chart?.result?.[0]?.meta;
    const price = meta?.regularMarketPrice;
    const prevClose = meta?.chartPreviousClose ?? meta?.previousClose;
    if (typeof price !== 'number') return null;

    const diff = typeof prevClose === 'number' ? price - prevClose : 0;
    const signed = formatSigned(diff, 2);
    return {
      name: displayName,
      value: formatNumber(price, 2),
      change: signed.text,
      isPositive: signed.isPositive,
    };
  } catch {
    return null;
  }
}

async function fetchGoldPavan(): Promise<MarketIndex | null> {
  try {
    const [goldRes, fxRes] = await Promise.all([
      fetch('https://api.gold-api.com/price/XAU', {
        signal: AbortSignal.timeout(4500),
        cache: 'no-store',
      }).then((r) => (r.ok ? r.json() : null)),
      fetch('https://open.er-api.com/v6/latest/USD', {
        signal: AbortSignal.timeout(4500),
        cache: 'no-store',
      }).then((r) => (r.ok ? r.json() : null)),
    ]);

    const xauUsd = goldRes?.price;
    const usdInr = fxRes?.rates?.INR;
    if (typeof xauUsd !== 'number' || typeof usdInr !== 'number') {
      return null;
    }

    // 1 troy oz = 31.1034768g; 1 pavan = 8g of 22K gold
    const inrPerGram24k = (xauUsd / 31.1034768) * usdInr;
    const inrPerPavan22k = Math.round(inrPerGram24k * (22 / 24) * 8);

    return {
      name: 'സ്വർണ്ണം (പവൻ)',
      value: `₹${inrPerPavan22k.toLocaleString('en-IN')}`,
      change: '22K/8g',
      isPositive: true,
    };
  } catch {
    return null;
  }
}

async function fetchMarket(): Promise<MarketIndex[]> {
  if (marketCache && marketCache.expiresAt > Date.now()) {
    return marketCache.data;
  }

  const [sensex, nifty, gold] = await Promise.all([
    fetchYahooIndex('^BSESN', 'SENSEX'),
    fetchYahooIndex('^NSEI', 'NIFTY 50'),
    fetchGoldPavan(),
  ]);

  const fallback = MARKET_INDICES;
  const result: MarketIndex[] = [
    sensex || fallback[0],
    nifty || fallback[1],
    gold || fallback[2],
  ];

  marketCache = { expiresAt: Date.now() + CACHE_TTL_MS, data: result };
  return result;
}

export async function GET(req: NextRequest) {
  const { searchParams } = new URL(req.url);
  const edition = searchParams.get('edition') || 'kerala-central';
  const latParam = searchParams.get('lat');
  const lonParam = searchParams.get('lon');
  const cityParam = searchParams.get('city') || undefined;

  const customLat = latParam !== null && !Number.isNaN(Number(latParam)) ? Number(latParam) : undefined;
  const customLon = lonParam !== null && !Number.isNaN(Number(lonParam)) ? Number(lonParam) : undefined;

  const [weather, market] = await Promise.all([
    fetchWeather(edition, customLat, customLon, cityParam),
    fetchMarket(),
  ]);

  return NextResponse.json(
    {
      weather,
      market,
      updatedAt: new Date().toISOString(),
    },
    { status: 200 }
  );
}
