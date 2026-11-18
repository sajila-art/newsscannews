export interface CategoryItem {
  id: string;
  slug?: string;
  nameMalayalam: string;
  nameEnglish: string;
  description?: string;
  homePage?: number | boolean;
}

export interface Article {
  id: string;
  title: string;
  subtitle?: string;
  excerpt: string;
  content: string[];
  category: string;
  categorySlug: string;
  author: {
    name: string;
    role: string;
    avatar?: string;
  };
  publishedAt: string;
  readTime: string;
  imageUrl: string;
  imageCaption?: string;
  tags: string[];
  isBreaking?: boolean;
  isFeatured?: boolean;
  isTrending?: boolean;
  scannedPageRef?: {
    pageNumber: number;
    editionName: string;
  };
  views?: number;
}

export interface ClippingZone {
  id: string;
  articleId: string;
  headline: string;
  category: string;
  topPercent: number;
  leftPercent: number;
  widthPercent: number;
  heightPercent: number;
}

export interface ScanPage {
  pageNumber: number;
  title: string;
  editionName: string;
  date: string;
  thumbnail: string;
  fullImageUrl: string;
  clippings: ClippingZone[];
}

export interface Edition {
  id: string;
  name: string;
  city: string;
  date: string;
  pageCount: number;
  tagline: string;
}

export interface MarketIndex {
  name: string;
  value: string;
  change: string;
  isPositive: boolean;
}
