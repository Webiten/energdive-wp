import { useEffect, useState } from "react";

export interface IntelligenceItem {
  id: number;
  title: string;
  category: string;
  author: string;
  date: string;
  readTime: string;
  excerpt: string;
  views?: number;
}

export function useIntelligence(limit?: number) {
  const [data, setData] = useState<IntelligenceItem[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetch(`/wp-json/energ/v1/intelligence`)
      .then((res) => res.json())
      .then((res) => {
        const items = limit ? res.slice(0, limit) : res;
        setData(items);
      })
      .finally(() => setLoading(false));
  }, [limit]);

  return { data, loading };
}
