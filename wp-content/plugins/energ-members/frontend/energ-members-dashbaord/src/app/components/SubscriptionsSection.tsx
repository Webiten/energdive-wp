import { Card, CardContent, CardHeader, CardTitle } from "./ui/card";
import { Button } from "./ui/button";
import { Badge } from "./ui/badge";
import { Check } from "lucide-react";

const plans = [
  {
    name: "Basic",
    price: "Free",
    period: "",
    description: "Perfect for getting started with energy industry insights",
    features: [
      "Access to selected articles",
      "Monthly newsletter",
      "Community discussions (view only)",
      "Limited event access"
    ],
    current: false
  },
  {
    name: "Professional",
    price: "$49",
    period: "/month",
    description: "For practitioners and consultants seeking comprehensive insights",
    features: [
      "Full access to all articles and analysis",
      "Weekly intelligence briefings",
      "Community participation",
      "All webinars and digital dialogues",
      "Bookmark and save content",
      "Priority event registration"
    ],
    current: true,
    popular: true
  },
  {
    name: "Executive",
    price: "$149",
    period: "/month",
    description: "For senior leaders and decision-makers",
    features: [
      "Everything in Professional",
      "Executive intelligence reports",
      "Exclusive executive lounges",
      "Data tools and AI insights",
      "Custom research requests",
      "1-on-1 analyst consultations",
      "Premium networking events"
    ],
    current: false
  },
  {
    name: "Corporate",
    price: "Custom",
    period: "",
    description: "Tailored solutions for organizations and teams",
    features: [
      "Everything in Executive",
      "Team accounts (5+ users)",
      "Corporate dashboard",
      "Custom content curation",
      "Dedicated account manager",
      "On-site workshops and training",
      "White-label research options"
    ],
    current: false
  }
];

export function SubscriptionsSection() {
  return (
    <div className="flex-1 bg-gray-50 overflow-auto">
      <div className="max-w-7xl mx-auto p-6 md:p-8 space-y-8">
        <div className="text-center">
          <h1 className="text-3xl font-semibold text-gray-900 mb-2">Subscription Plans</h1>
          <p className="text-gray-600">Choose the plan that best fits your needs</p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          {plans.map((plan) => (
            <Card
              key={plan.name}
              className={`relative ${
                plan.popular
                  ? "border-emerald-500 shadow-lg"
                  : ""
              }`}
            >
              {plan.popular && (
                <div className="absolute -top-3 left-1/2 transform -translate-x-1/2">
                  <Badge className="bg-emerald-600">Most Popular</Badge>
                </div>
              )}
              {plan.current && (
                <div className="absolute top-4 right-4">
                  <Badge variant="secondary">Current Plan</Badge>
                </div>
              )}
              <CardHeader>
                <CardTitle className="text-2xl">{plan.name}</CardTitle>
                <div className="mt-4">
                  <span className="text-4xl font-bold text-gray-900">{plan.price}</span>
                  {plan.period && (
                    <span className="text-gray-600">{plan.period}</span>
                  )}
                </div>
                <p className="text-sm text-gray-600 mt-2">{plan.description}</p>
              </CardHeader>
              <CardContent>
                <ul className="space-y-3 mb-6">
                  {plan.features.map((feature, index) => (
                    <li key={index} className="flex items-start gap-2">
                      <Check className="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" />
                      <span className="text-sm text-gray-700">{feature}</span>
                    </li>
                  ))}
                </ul>
                {plan.current ? (
                  <Button className="w-full" variant="outline" disabled>
                    Current Plan
                  </Button>
                ) : (
                  <Button
                    className={`w-full ${
                      plan.popular
                        ? "bg-emerald-600 hover:bg-emerald-700"
                        : ""
                    }`}
                    variant={plan.popular ? "default" : "outline"}
                  >
                    {plan.name === "Corporate" ? "Contact Sales" : "Upgrade Now"}
                  </Button>
                )}
              </CardContent>
            </Card>
          ))}
        </div>

        {/* Additional Information */}
        <Card className="bg-gradient-to-br from-emerald-50 to-emerald-100 border-emerald-200">
          <CardContent className="pt-6">
            <h3 className="text-lg font-semibold text-gray-900 mb-2">
              Need a custom solution?
            </h3>
            <p className="text-gray-700 mb-4">
              Our Corporate plan can be tailored to meet your organization's specific needs. 
              Contact our sales team to discuss custom pricing and features.
            </p>
            <Button className="bg-emerald-600 hover:bg-emerald-700">
              Contact Sales Team
            </Button>
          </CardContent>
        </Card>
      </div>
    </div>
  );
}