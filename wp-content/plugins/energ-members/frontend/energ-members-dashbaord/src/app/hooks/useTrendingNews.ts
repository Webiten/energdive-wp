import { useEffect, useState } from "react";

export interface TrendingNewsItem {
  id: number;
  title: string;
  category: string;
  views?: number;
}

export function useTrendingNews(limit = 4) {
  const [news, setNews] = useState<TrendingNewsItem[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetch(`/wp-json/wp/v2/news?per_page=${limit}&orderby=date&order=desc`)
      .then((res) => res.json())
      .then((data) => {
        const formatted = data.map((item: any) => ({
          id: item.id,
          title: item.title.rendered,
          category:
            item._embedded?.["wp:term"]?.[0]?.[0]?.name || "News",
          views: item.meta?.views || 0,
        }));

        setNews(formatted);
      })
      .finally(() => setLoading(false));
  }, [limit]);

  return { news, loading };
}
