import { useEffect, useState } from "react";
import { Card, CardContent, CardHeader, CardTitle } from "./ui/card";
import { Badge } from "./ui/badge";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "./ui/tabs";
import { FileText } from "lucide-react";

/* ---------------- TYPES ---------------- */

type IntelligenceArticle = {
  id: number;
  title: string;
  url: string;
};

type IntelligenceItem = {
  id: number;
  title: string;
  excerpt: string;
  read_time: string;
  sector: string;
  articles: IntelligenceArticle[];
};

/* ---------------- COMPONENT ---------------- */

export function IntelligenceSection() {
  const [data, setData] = useState<IntelligenceItem[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const token = localStorage.getItem("access_token");

    fetch("/wp-json/energ/v1/intelligence", {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
      .then((res) => res.json())
      .then((res) => {
        setData(Array.isArray(res) ? res : []);
      })
      .finally(() => setLoading(false));
  }, []);

  if (loading) {
    return <div className="p-6">Loading intelligence…</div>;
  }

  if (!data.length) {
    return <div className="p-6">No intelligence available.</div>;
  }

  return (
    <div className="flex-1 bg-gray-50 overflow-auto">
      <div className="max-w-7xl mx-auto p-6 md:p-8">
        {/* Header */}
        <div className="mb-8">
          <h1 className="text-3xl font-semibold mb-2">Intelligence</h1>
          <p className="text-gray-600">
            Expert analysis, insights, and industry deep dives
          </p>
        </div>

        {/* Tabs by Sector */}
        <Tabs defaultValue={String(data[0].id)}>
          <TabsList className="mb-6 flex flex-wrap gap-2">
            {data.map((item) => (
              <TabsTrigger key={item.id} value={String(item.id)}>
                <FileText className="w-4 h-4 mr-2" />
                {item.sector}
              </TabsTrigger>
            ))}
          </TabsList>

          {data.map((item) => (
            <TabsContent key={item.id} value={String(item.id)}>
              <div className="space-y-6">
                {/* Intelligence Card */}
                <Card>
                  <CardHeader>
                    <div className="flex justify-between mb-2">
                      <Badge variant="secondary">{item.sector}</Badge>
                      <span className="text-sm text-gray-500">
                        {item.read_time}
                      </span>
                    </div>
                    <CardTitle className="text-2xl">
                      {item.title}
                    </CardTitle>
                  </CardHeader>

                  <CardContent>
                    {/* Articles List */}
                    {item.articles.length ? (
                      <ul className="space-y-3">
                        {item.articles.map((a) => (
                          <li key={a.id}>
                            <a
                              href={a.url}
                              target="_blank"
                              rel="noreferrer"
                              className="text-emerald-600 hover:underline font-medium"
                            >
                              {a.title}
                            </a>
                          </li>
                        ))}
                      </ul>
                    ) : (
                      <p className="text-gray-500">
                        No articles mapped yet.
                      </p>
                    )}
                  </CardContent>
                </Card>
              </div>
            </TabsContent>
          ))}
        </Tabs>
      </div>
    </div>
  );
}
