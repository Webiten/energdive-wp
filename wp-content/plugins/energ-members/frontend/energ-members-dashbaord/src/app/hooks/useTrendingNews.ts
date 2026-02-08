import { useEffect, useState } from "react";
import { NewsAPI } from "../lib/api";

export interface TrendingNewsItem {
  id: number;
  title: string;
  category: string;
  views: number;
}

export function useTrendingNews(limit = 4) {
  const [data, setData] = useState<TrendingNewsItem[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    async function fetchTrending() {
      setLoading(true);
      setError(null);

      try {
        const res = await NewsAPI.getNews();

        const formatted = (res.news || [])
          .slice(0, limit)
          .map((item: any) => ({
            id: item.id,
            title: item.title,
            category: "News",
            views: Math.floor(Math.random() * 5000) + 100, // temp mock views
          }));

        setData(formatted);
      } catch (err: any) {
        setError(err?.message || "Failed to load trending news");
      } finally {
        setLoading(false);
      }
    }

    fetchTrending();
  }, [limit]);

  return { data, loading, error };
}
