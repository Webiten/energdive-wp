import { useEffect, useState } from "react";
import { Card, CardContent, CardHeader, CardTitle } from "./ui/card";
import { Badge } from "./ui/badge";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "./ui/tabs";
import { FileText } from "lucide-react";

type IntelligenceItem = {
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
  const [articles, setArticles] = useState<IntelligenceItem[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchIntelligence = async () => {
      try {
        const token = localStorage.getItem("energ_token");
        if (!token) {
          setLoading(false);
          return;
        }

        const res = await fetch(
          `${(window as any).ENERG?.api}/intelligence`,
          {
            headers: {
              Authorization: `Bearer ${token}`,
            },
          }
        );

        const data = await res.json();
        setArticles(Array.isArray(data) ? data : []);
      } catch (err) {
        console.error("Failed to load intelligence", err);
        setArticles([]);
      } finally {
        setLoading(false);
      }
    };

    fetchIntelligence();
  }, []);

  return (
    <div className="flex-1 bg-gray-50 overflow-auto">
      <div className="max-w-7xl mx-auto p-6 md:p-8">
        <div className="mb-8">
          <h1 className="text-3xl font-semibold text-gray-900 mb-2">
            Intelligence
          </h1>
          <p className="text-gray-600">
            Latest insights based on your selected community
          </p>
        </div>

        <Tabs defaultValue="all" className="space-y-6">
          <TabsList>
            <TabsTrigger value="all" className="flex items-center gap-2">
              <FileText className="w-4 h-4" />
              All
            </TabsTrigger>
          </TabsList>

          <TabsContent value="all">
            {loading && (
              <p className="text-gray-500">Loading intelligence…</p>
            )}

            {!loading && articles.length === 0 && (
              <p className="text-gray-500">
                No intelligence available for your community.
              </p>
            )}

            <div className="grid gap-6">
              {articles.map((article) => (
                <Card
                  key={article.id}
                  className="hover:shadow-lg transition-shadow cursor-pointer"
                  onClick={() => window.open(article.url, "_blank")}
                >
                  <CardHeader>
                    <div className="flex items-start justify-between mb-2">
                      <Badge variant="secondary">
                        {article.category || "Intelligence"}
                      </Badge>
                      <span className="text-sm text-gray-500">
                        {article.read_time}
                      </span>
                    </div>
                    <CardTitle className="text-2xl hover:text-emerald-600 transition-colors">
                      {article.title}
                    </CardTitle>
                  </CardHeader>

                  <CardContent>
                    <p className="text-gray-600 mb-4">
                      {article.excerpt}
                    </p>
                    <div className="flex items-center justify-between text-sm text-gray-500">
                      <span className="font-medium">{article.author}</span>
                      <span>{article.date}</span>
                    </div>
                  </CardContent>
                </Card>
              ))}
            </div>
          </TabsContent>
        </Tabs>
      </div>
    </div>
  );
}
