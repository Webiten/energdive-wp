import { useEffect, useState } from "react";
import { Card, CardContent, CardHeader, CardTitle } from "./ui/card";
import { Badge } from "./ui/badge";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "./ui/tabs";
import { FileText } from "lucide-react";

type Article = {
  id: number;
  title: string;
  excerpt: string;
  url: string;
  date: string;
  read_time: string;
  author: string;
  category: string;
};

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
        setArticles(Array.isArray(data) ? data : []);
      })
      .finally(() => setLoading(false));
  }, []);

  const grouped = articles.reduce<Record<string, Article[]>>((acc, item) => {
    const key = item.category || "General";
    acc[key] = acc[key] || [];
    acc[key].push(item);
    return acc;
  }, {});

  const categories = Object.keys(grouped);

  if (loading) {
    return <div className="p-6">Loading intelligence…</div>;
  }

  if (!articles.length) {
    return <div className="p-6">No intelligence available.</div>;
  }

  return (
    <div className="flex-1 bg-gray-50 overflow-auto">
      <div className="max-w-7xl mx-auto p-6 md:p-8">
        <h1 className="text-3xl font-semibold mb-2">Intelligence</h1>
        <p className="text-gray-600 mb-6">
          Expert analysis, insights, and industry deep dives
        </p>

        <Tabs defaultValue={categories[0]}>
          <TabsList className="mb-6 flex flex-wrap gap-2">
            {categories.map((cat) => (
              <TabsTrigger key={cat} value={cat}>
                <FileText className="w-4 h-4 mr-2" />
                {cat}
              </TabsTrigger>
            ))}
          </TabsList>

          {categories.map((cat) => (
            <TabsContent key={cat} value={cat}>
              <div className="space-y-6">
                {grouped[cat].map((a) => (
                  <Card key={a.id} onClick={() => window.open(a.url, "_blank")}>
                    <CardHeader>
                      <div className="flex justify-between mb-2">
                        <Badge variant="secondary">{cat}</Badge>
                        <span className="text-sm text-gray-500">
                          {a.read_time}
                        </span>
                      </div>
                      <CardTitle className="text-xl hover:text-emerald-600">
                        {a.title}
                      </CardTitle>
                    </CardHeader>
                    <CardContent>
                      <p className="text-gray-600 mb-3">{a.excerpt}</p>
                      <div className="flex justify-between text-sm text-gray-500">
                        <span>{a.author}</span>
                        <span>{a.date}</span>
                      </div>
                    </CardContent>
                  </Card>
                ))}
              </div>
            </TabsContent>
          ))}
        </Tabs>
      </div>
    </div>
  );
}
