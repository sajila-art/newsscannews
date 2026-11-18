import { Article, CategoryItem } from '../types/news';
import { CATEGORIES_LIST } from '../data/mockData';

const DEFAULT_API_URL = 'http://localhost/e_news/backend';
const CONFIGURED_API_URL = process.env.NEXT_PUBLIC_API_URL?.replace(/\/+$/, '');
const API_BASE_URL = CONFIGURED_API_URL || DEFAULT_API_URL;

interface ApiResponse<T> {
  data: T;
  error?: string;
}

export class NewsApiError extends Error {
  constructor(
    message: string,
    public readonly status: number,
  ) {
    super(message);
    this.name = 'NewsApiError';
  }
}

async function tryFetchJson<T>(url: string): Promise<T | null> {
  try {
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 4000);

    const response = await fetch(url, {
      cache: 'no-store',
      headers: { Accept: 'application/json' },
      signal: controller.signal,
    });
    clearTimeout(timeoutId);

    if (!response.ok) {
      return null;
    }

    const payload = (await response.json()) as ApiResponse<T>;
    return payload.data ?? null;
  } catch {
    return null;
  }
}

async function request<T>(path: string): Promise<T> {
  // Candidate base URLs to handle Apache mod_rewrite & Windows IPv6/IPv4 localhost nuances
  const candidates: string[] = [];
  const base = API_BASE_URL.replace(/\/+$/, '');

  candidates.push(`${base}/api/v1${path}`);
  candidates.push(`${base}/index.php/api/v1${path}`);

  if (base.includes('localhost')) {
    const ipv4Base = base.replace('localhost', '127.0.0.1');
    candidates.push(`${ipv4Base}/api/v1${path}`);
    candidates.push(`${ipv4Base}/index.php/api/v1${path}`);
  }

  for (const url of candidates) {
    const result = await tryFetchJson<T>(url);
    if (result !== null) {
      return result;
    }
  }

  throw new NewsApiError(`Unable to fetch from API at ${path} on any candidate URL.`, 502);
}

export function isNewsApiConfigured(): boolean {
  return true;
}

export async function getArticles(query = ''): Promise<Article[]> {
  try {
    return await request<Article[]>(`/articles${query}`);
  } catch (err) {
    console.warn('API getArticles failed, falling back to mockData:', err);
    return [];
  }
}

export async function getArticle(id: string): Promise<Article | null> {
  try {
    return await request<Article>(`/articles/${encodeURIComponent(id)}`);
  } catch {
    return null;
  }
}

export interface HomeBannerData {
  id?: number;
  title: string;
  subtitle?: string | null;
  description: string;
  author: {
    name: string;
    role?: string;
  };
  bannerImage?: string | null;
  badge?: string | null;
  linkUrl?: string | null;
  status?: string;
  updatedAt?: string;
}

export async function getHomeBanner(): Promise<HomeBannerData | null> {
  try {
    return await request<HomeBannerData>('/home');
  } catch (err) {
    console.warn('API getHomeBanner failed:', err);
    return null;
  }
}

export interface TodaysAudioData {
  id: number;
  audioDate: string;
  title: string;
  scriptText: string;
  audioUrl: string | null;
  articleCount: number;
  durationLabel: string;
  status: string;
  createdAt?: string;
}

export async function getTodaysAudio(): Promise<TodaysAudioData | null> {
  try {
    return await request<TodaysAudioData>('/todays-audio');
  } catch (err) {
    console.warn('API getTodaysAudio failed:', err);
    return null;
  }
}

export interface GetCategoriesOptions {
  homeOnly?: boolean;
  notHomeOnly?: boolean;
}

export async function getCategories(options?: GetCategoriesOptions): Promise<CategoryItem[]> {
  try {
    let query = '';
    if (options?.homeOnly) {
      query = '?home_page=1';
    } else if (options?.notHomeOnly) {
      query = '?home_page_not=1';
    }

    const categories = await request<CategoryItem[]>(`/categories${query}`);

    if (Array.isArray(categories) && categories.length > 0) {
      if (options?.homeOnly) {
        const filtered = categories.filter((c) => c.homePage === undefined || Number(c.homePage) === 1 || c.homePage === true);
        return filtered.length > 0 ? filtered : categories;
      }
      if (options?.notHomeOnly) {
        return categories.filter((c) => Number(c.homePage) !== 1 && c.homePage !== true);
      }
      return categories;
    }
  } catch (err) {
    console.warn('API getCategories failed, falling back to CATEGORIES_LIST:', err);
  }

  // Graceful fallback to CATEGORIES_LIST
  if (options?.homeOnly) {
    return CATEGORIES_LIST.filter((c) => Number(c.homePage) === 1 || c.homePage === true);
  }
  if (options?.notHomeOnly) {
    return CATEGORIES_LIST.filter((c) => Number(c.homePage) !== 1 && c.homePage !== true);
  }
  return CATEGORIES_LIST;
}
