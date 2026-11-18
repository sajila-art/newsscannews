import { NextResponse } from 'next/server';
import { getCategories } from '@/lib/newsApi';

export const dynamic = 'force-dynamic';

export async function GET(request: Request) {
  const { searchParams } = new URL(request.url);
  const homeOnly = searchParams.get('home_page') === '1';
  const notHomeOnly = searchParams.get('home_page_not') === '1' || searchParams.get('home_page') === '0';

  try {
    const categories = await getCategories({ homeOnly, notHomeOnly });
    return NextResponse.json({ status: 'success', data: categories });
  } catch (err: any) {
    return NextResponse.json(
      { status: 'error', message: err?.message || 'Failed to fetch categories' },
      { status: 500 }
    );
  }
}
