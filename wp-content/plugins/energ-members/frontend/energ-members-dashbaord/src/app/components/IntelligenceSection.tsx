import { useEffect, useState } from "react";
import { Card, CardContent, CardHeader, CardTitle } from "./ui/card";
import { Badge } from "./ui/badge";

interface Article {
  id: number;
  title: string;
  excerpt: string;
  author: string;
  date: string;
  read_time: string;
  category: string;
  url: string;
}

export function IntelligenceSection() {
  const [articles, setArticles] = useState<Article[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const token = localStorage.getItem("access_token");

    fetch("/wp-json/energ/v1/intelligence", {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
      .then((res) => res.json())
      .then((data) => {
        setArticles(data || []);
        setLoading(false);
      })
      .catch(() => setLoading(false));
  }, []);

  if (loading) {
    return <div className="p-6">Loading intelligence...</div>;
  }

  if (!articles.length) {
    return <div className="p-6">No intelligence available</div>;
  }

  return (
    <div className="flex-1 bg-gray-50 overflow-auto">
      <div className="max-w-7xl mx-auto p-6 md:p-8">
        <h1 className="text-3xl font-semibold mb-2">Intelligence</h1>
        <p className="text-gray-600 mb-6">
          Expert analysis, insights, and industry deep dives
        </p>

        <div className="grid gap-6">
          {articles.map((article) => (
            <Card
              key={article.id}
              className="hover:shadow-lg transition-shadow cursor-pointer"
              onClick={() => window.open(article.url, "_blank")}
            >
              <CardHeader>
                <div className="flex items-center justify-between mb-2">
                  <Badge variant="secondary">{article.category}</Badge>
                  <span className="text-sm text-gray-500">
                    {article.read_time}
                  </span>
                </div>
                <CardTitle className="text-2xl hover:text-emerald-600">
                  {article.title}
                </CardTitle>
              </CardHeader>

              <CardContent>
                <p className="text-gray-600 mb-4">{article.excerpt}</p>
                <div className="flex justify-between text-sm text-gray-500">
                  <span>{article.author}</span>
                  <span>{article.date}</span>
                </div>
              </CardContent>
            </Card>
          ))}
        </div>
      </div>
    </div>
  );
}
