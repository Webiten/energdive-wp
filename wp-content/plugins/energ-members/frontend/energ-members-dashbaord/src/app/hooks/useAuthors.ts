import { useEffect, useState } from "react";
import { AuthorAPI } from "../lib/api";

export interface AuthorItem {
  id: number;
  name: string;
  role: string;
  specialty?: string;
  avatar?: string | null;
}

export function useAuthors(limit = 5) {
  const [authors, setAuthors] = useState<AuthorItem[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    async function fetchAuthors() {
      setLoading(true);
      setError(null);

      try {
        const res = await AuthorAPI.getAuthors();
        setAuthors((res.authors || []).slice(0, limit));
      } catch (err: any) {
        setError(err?.message || "Failed to load authors");
      } finally {
        setLoading(false);
      }
    }

    fetchAuthors();
  }, [limit]);

  return { authors, loading, error };
}
