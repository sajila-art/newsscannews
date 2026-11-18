import { NextRequest, NextResponse } from 'next/server';

export const dynamic = 'force-dynamic';

const CANDIDATE_API_BASES = [
  process.env.NEXT_PUBLIC_API_BASE_URL,
  'http://127.0.0.1/e_news/backend/index.php/api/v1',
  'http://localhost/e_news/backend/index.php/api/v1',
].filter((v): v is string => Boolean(v && v.trim()));

export async function GET(req: NextRequest) {
  const { searchParams } = new URL(req.url);
  const q = (searchParams.get('q') ?? '').trim();

  for (const base of CANDIDATE_API_BASES) {
    const url = `${base.replace(/\/+$/, '')}/search?q=${encodeURIComponent(q)}`;
    try {
      const res = await fetch(url, {
        headers: { Accept: 'application/json' },
        cache: 'no-store',
        signal: AbortSignal.timeout(5000),
      });
      if (!res.ok) continue;
      const payload = await res.json();
      if (payload?.data) {
        return NextResponse.json(payload.data, { status: 200 });
      }
    } catch {
      // Try next candidate URL
    }
  }

  return NextResponse.json(
    { categories: [], articles: [], error: 'Unable to search database right now.' },
    { status: 502 }
  );
}
