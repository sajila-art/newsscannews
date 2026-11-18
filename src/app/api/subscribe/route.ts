import { NextRequest, NextResponse } from 'next/server';

const CANDIDATE_API_BASES = [
  process.env.NEXT_PUBLIC_API_BASE_URL,
  'http://127.0.0.1/e_news/backend/index.php/api/v1',
  'http://localhost/e_news/backend/index.php/api/v1',
].filter((v): v is string => Boolean(v && v.trim()));

export async function POST(req: NextRequest) {
  let body: { email?: string } = {};
  try {
    body = await req.json();
  } catch {
    return NextResponse.json({ error: 'Invalid request body' }, { status: 400 });
  }

  const email = (body.email ?? '').trim();
  if (!email) {
    return NextResponse.json({ error: 'Email is required' }, { status: 400 });
  }

  const forwardedFor = req.headers.get('x-forwarded-for');
  const clientIp = forwardedFor ? forwardedFor.split(',')[0].trim() : req.headers.get('x-real-ip') || '';

  for (const base of CANDIDATE_API_BASES) {
    const url = `${base.replace(/\/+$/, '')}/subscribe`;
    try {
      const res = await fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          ...(clientIp ? { 'X-Forwarded-For': clientIp } : {}),
        },
        body: JSON.stringify({ email }),
        cache: 'no-store',
      });

      const payload = await res.json().catch(() => null);
      if (res.ok) {
        return NextResponse.json(payload ?? { data: { status: 'subscribed' } }, { status: res.status });
      }
      if (res.status >= 400 && res.status < 500) {
        return NextResponse.json(
          { error: payload?.error || 'Subscription failed' },
          { status: res.status }
        );
      }
    } catch {
      // Try next candidate URL
    }
  }

  return NextResponse.json(
    { error: 'Unable to reach subscription service' },
    { status: 502 }
  );
}
