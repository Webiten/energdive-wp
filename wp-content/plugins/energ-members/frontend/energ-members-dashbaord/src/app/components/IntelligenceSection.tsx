import { useEffect, useState } from "react";
import { Card, CardContent, CardHeader, CardTitle } from "./ui/card";
import { Badge } from "./ui/badge";

type Article = {
  id: number;
  title: string;
  excerpt: string;
  read_time: string;
  articles: {
    id: number;
    title: string;
    url: string;
  }[];
};

type IntelligenceGroup = {
  sector: string;
  items: Article[];
};

export function IntelligenceSection() {
  const [groups, setGroups] = useState<IntelligenceGroup[]>([]);
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
        setGroups(Array.isArray(data) ? data : []);
      })
      .finally(() => setLoading(false));
  }, []);

  if (loading) {
    return <div className="p-6">Loading intelligence…</div>;
  }

  if (!groups.length) {
    return <div className="p-6">No intelligence available.</div>;
  }

  return (
    <div className="flex-1 bg-gray-50 overflow-auto">
      <div className="max-w-7xl mx-auto p-6 md:p-8 space-y-10">
        <div>
          <h1 className="text-3xl font-semibold mb-2">Intelligence</h1>
          <p className="text-gray-600">
            Expert analysis, insights, and industry deep dives
          </p>
        </div>

        {groups.map((group) => (
          <div key={group.sector} className="space-y-4">
            <Badge variant="outline" className="text-sm">
              {group.sector}
            </Badge>

            {group.items.length === 0 && (
              <p className="text-sm text-gray-500">
                No intelligence available for this sector.
              </p>
            )}

            {group.items.map((item) => (
              <Card key={item.id}>
                <CardHeader>
                  <div className="flex justify-between mb-2">
                    <span className="text-sm text-gray-500">
                      {item.read_time}
                    </span>
                  </div>
                  <CardTitle className="text-xl">
                    {item.title}
                  </CardTitle>
                </CardHeader>

                <CardContent className="space-y-2">
                  {item.articles.map((a) => (
                    <a
                      key={a.id}
                      href={a.url}
                      target="_blank"
                      className="block text-emerald-600 hover:underline text-sm"
                    >
                      {a.title}
                    </a>
                  ))}
                </CardContent>
              </Card>
            ))}
          </div>
        ))}
      </div>
    </div>
  );
}
