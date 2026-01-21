import { Card, CardContent, CardHeader, CardTitle } from "./ui/card";
import { Badge } from "./ui/badge";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "./ui/tabs";
import { FileText, Scale, TrendingUp, Lightbulb } from "lucide-react";

const categories = [
  {
    id: "editorial",
    label: "Editorial Analysis",
    icon: FileText,
    articles: [
      {
        title: "Energy Transition: Navigating the Decade Ahead",
        author: "Editorial Team",
        date: "Jan 9, 2026",
        readTime: "15 min",
        excerpt: "A comprehensive editorial examining the key drivers and challenges in the global energy transition through 2035."
      },
      {
        title: "The New Energy Landscape: Winners and Losers",
        author: "Chief Editor",
        date: "Jan 5, 2026",
        readTime: "12 min",
        excerpt: "Analysis of how the shifting energy paradigm is creating new opportunities while disrupting traditional business models."
      }
    ]
  },
  {
    id: "policy",
    label: "Policy & Regulation",
    icon: Scale,
    articles: [
      {
        title: "EU Carbon Border Adjustment: Implementation Guide",
        author: "Dr. Helena Schmidt",
        date: "Jan 8, 2026",
        readTime: "18 min",
        excerpt: "Detailed breakdown of CBAM regulations and compliance requirements for energy-intensive industries."
      },
      {
        title: "US IRA: Two Years On - Impact Assessment",
        author: "Policy Analysis Team",
        date: "Jan 4, 2026",
        readTime: "20 min",
        excerpt: "Comprehensive review of the Inflation Reduction Act's effects on clean energy deployment and manufacturing."
      }
    ]
  },
  {
    id: "market",
    label: "Market & Industry",
    icon: TrendingUp,
    articles: [
      {
        title: "Global Electricity Markets: Q4 2025 Review",
        author: "Market Intelligence Team",
        date: "Jan 7, 2026",
        readTime: "14 min",
        excerpt: "Quarterly analysis of power market dynamics, pricing trends, and capacity developments worldwide."
      },
      {
        title: "Oil & Gas M&A Activity: 2025 Year in Review",
        author: "Financial Analysts",
        date: "Jan 3, 2026",
        readTime: "16 min",
        excerpt: "Assessment of mergers, acquisitions, and strategic partnerships that shaped the energy sector in 2025."
      }
    ]
  },
  {
    id: "technology",
    label: "Technology & Innovation",
    icon: Lightbulb,
    articles: [
      {
        title: "Next-Gen Grid Technologies: 2026 Outlook",
        author: "Tech Innovation Team",
        date: "Jan 6, 2026",
        readTime: "13 min",
        excerpt: "Exploration of emerging smart grid technologies, from AI-driven optimization to advanced metering infrastructure."
      },
      {
        title: "Green Hydrogen: Scaling Challenges and Solutions",
        author: "Dr. Rajesh Kumar",
        date: "Jan 2, 2026",
        readTime: "17 min",
        excerpt: "Technical deep dive into the engineering and economic hurdles facing large-scale hydrogen production and distribution."
      }
    ]
  }
];

export function IntelligenceSection() {
  return (
    <div className="flex-1 bg-gray-50 overflow-auto">
      <div className="max-w-7xl mx-auto p-6 md:p-8">
        <div className="mb-8">
          <h1 className="text-3xl font-semibold text-gray-900 mb-2">Intelligence</h1>
          <p className="text-gray-600">Expert analysis, insights, and industry deep dives</p>
        </div>

        <Tabs defaultValue="editorial" className="space-y-6">
          <TabsList className="grid w-full grid-cols-4 lg:w-auto lg:inline-grid">
            {categories.map((category) => {
              const Icon = category.icon;
              return (
                <TabsTrigger key={category.id} value={category.id} className="flex items-center gap-2">
                  <Icon className="w-4 h-4" />
                  <span className="hidden lg:inline">{category.label}</span>
                  <span className="lg:hidden">{category.label.split(' ')[0]}</span>
                </TabsTrigger>
              );
            })}
          </TabsList>

          {categories.map((category) => (
            <TabsContent key={category.id} value={category.id}>
              <div className="grid gap-6">
                {category.articles.map((article, index) => (
                  <Card key={index} className="hover:shadow-lg transition-shadow cursor-pointer">
                    <CardHeader>
                      <div className="flex items-start justify-between mb-2">
                        <Badge variant="secondary">{category.label}</Badge>
                        <span className="text-sm text-gray-500">{article.readTime} read</span>
                      </div>
                      <CardTitle className="text-2xl hover:text-emerald-600 transition-colors">
                        {article.title}
                      </CardTitle>
                    </CardHeader>
                    <CardContent>
                      <p className="text-gray-600 mb-4">{article.excerpt}</p>
                      <div className="flex items-center justify-between text-sm text-gray-500">
                        <span className="font-medium">{article.author}</span>
                        <span>{article.date}</span>
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