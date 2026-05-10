export const bgColors = [
  'bg-blue-50', 'bg-pink-50', 'bg-purple-50', 'bg-yellow-50',
  'bg-orange-50', 'bg-green-50', 'bg-teal-50', 'bg-indigo-50', 'bg-amber-50',
];

export const badgeTabMap = {
  Hot:      'trending',
  Trending: 'trending',
  New:      'new',
  Sale:     'bestsellers',
};

export function normalizeProduct(p, index) {
  let price = p.price;
  if (p.type === 'variable') {
    price = p.variations_min_price != null
      ? parseFloat(p.variations_min_price).toFixed(2)
      : null;
  } else if (p.type === 'grouped') {
    price = null;
  }
  return {
    ...p,
    category:      p.category?.name ?? 'General',
    categoryId:    p.category_id,
    originalPrice: p.original_price,
    bg:            bgColors[index % bgColors.length],
    rating:        p.rating  ?? 0,
    reviews:       p.reviews ?? 0,
    tab:           badgeTabMap[p.badge] ?? 'trending',
    price,
  };
}

export function discountPercent(price, original) {
  if (!original || !price) return null;
  const p = parseFloat(price);
  const o = parseFloat(original);
  if (o <= p) return null;
  return Math.round(((o - p) / o) * 100);
}
