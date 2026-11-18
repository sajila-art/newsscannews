/** @type {import('next').NextConfig} */
const nextConfig = {
  reactStrictMode: true,
  images: {
    domains: [
      'images.unsplash.com',
      'plus.unsplash.com',
      'source.unsplash.com'
    ],
  },
  webpack: (config, { dev }) => {
    if (dev) {
      // Disable persistent filesystem cache in dev to avoid parallel gzip memory spikes on low-RAM systems
      config.cache = false;
    }
    return config;
  },
  onDemandEntries: {
    maxInactiveAge: 25 * 1000,
    pagesBufferLength: 2,
  },
};

module.exports = nextConfig;
