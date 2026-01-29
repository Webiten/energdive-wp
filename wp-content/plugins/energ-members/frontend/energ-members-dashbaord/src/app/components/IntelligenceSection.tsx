import { useEffect, useState } from "react";
import { Card, CardContent, CardHeader, CardTitle } from "./ui/card";
import { Badge } from "./ui/badge";

type Article = {
  id: number;
  title: string;
  url: string;
};

type IntelligenceGroup = {
  sector?: string;
  articles?: Article[];
};

export function IntelligenceSection() {
  const [groups, setGroups] = useState<IntelligenceGroup[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const token =
      localStorage.getItem("access_token") ||
      localStorage.getItem("auth_token");

    fetch("/wp-json/energ/v1/intelligence", {
      headers: {
        Authorization: token ? `Bearer ${token}` : "",
      },
    })
      .then((res) => {
        if (!res.ok) throw new Error("Unauthorized / API failed");
        return res.json();
      })
      .then((data) => {
        setGroups(Array.isArray(data) ? data : []);
      })
      .catch((err) => {
        console.error("Intelligence fetch failed:", err);
        setGroups([]);
      })
      .finally(() => setLoading(false));
  }, []);

  if (loading) {
    return <div className="p-6">Loading intelligence…</div>;
  }

  if (!Array.isArray(groups) || groups.length === 0) {
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

        {groups.map((group, idx) => {
          const articles = Array.isArray(group.articles)
            ? group.articles
            : [];

          return (
            <div key={idx} className="space-y-4">
              <Badge variant="outline" className="text-sm">
                {group.sector || "Intelligence"}
              </Badge>

              {articles.length === 0 ? (
                <p className="text-sm text-gray-500">
                  No intelligence available for this sector.
                </p>
              ) : (
                articles.map((article) => (
                  <Card key={article.id}>
                    <CardHeader>
                      <CardTitle className="text-lg">
                        {article.title}
                      </CardTitle>
                    </CardHeader>

                    <CardContent>
                      <a
                        href={article.url}
                        target="_blank"
                        rel="noopener noreferrer"
                        className="text-emerald-600 hover:underline text-sm"
                      >
                        Read article →
                      </a>
                    </CardContent>
                  </Card>
                ))
              )}
            </div>
          );
        })}
      </div>
    </div>
  );
}
