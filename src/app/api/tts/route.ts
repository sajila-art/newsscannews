import { NextRequest, NextResponse } from 'next/server';

export async function GET(req: NextRequest) {
  const { searchParams } = new URL(req.url);
  const text = (searchParams.get('q') || '').trim();
  const lang = (searchParams.get('lang') || 'ml').trim();

  if (!text) {
    return NextResponse.json({ error: 'Missing query text parameter q' }, { status: 400 });
  }

  // Keep chunk within safe TTS character limit
  const safeText = text.slice(0, 200);
  const ttsUrl = `https://translate.google.com/translate_tts?ie=UTF-8&client=tw-ob&tl=${encodeURIComponent(lang)}&q=${encodeURIComponent(safeText)}`;

  try {
    const upstream = await fetch(ttsUrl, {
      headers: {
        'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
      },
      cache: 'no-store',
    });

    if (!upstream.ok) {
      return NextResponse.json(
        { error: 'Upstream TTS unavailable' },
        { status: upstream.status }
      );
    }

    const audioBuffer = await upstream.arrayBuffer();
    return new NextResponse(audioBuffer, {
      status: 200,
      headers: {
        'Content-Type': 'audio/mpeg',
        'Cache-Control': 'public, max-age=86400',
      },
    });
  } catch {
    return NextResponse.json({ error: 'Failed to synthesize Malayalam audio' }, { status: 502 });
  }
}
