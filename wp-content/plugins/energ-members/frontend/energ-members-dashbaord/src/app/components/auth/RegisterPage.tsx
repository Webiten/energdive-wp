import { useEffect, useMemo, useState } from "react";
import { Card, CardContent, CardHeader, CardTitle } from "../ui/card";
import { Button } from "../ui/button";
import { Input } from "../ui/input";
import { Label } from "../ui/label";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "../ui/select";
import { Checkbox } from "../ui/checkbox";
import { CheckCircle, Loader2, Phone, AlertCircle, RefreshCw } from "lucide-react";
import { AuthAPI } from "@/app/lib/api";

/* =====================================================
   COMMUNITY LABEL MAP (🔥 IMPORTANT)
   ===================================================== */
const communityLabelMap: Record<string, string> = {
  "oil-gas": "Oil & Gas",
  "power-generation": "Power Generation",
  renewables: "Renewables",
  transmission: "Transmission",
  distribution: "Distribution",
  "electricity-markets": "Electricity Markets",
  "new-energies": "New Energies",
  "energy-storage-systems": "Energy Storage Systems",
  sustainability: "Sustainability",
};

interface RegisterPageProps {
  email: string;
  onRegistrationComplete: () => void;
}

/* =====================================================
   COMMUNITIES & SUB-COMMUNITIES
   ===================================================== */
const communities = [
  { value: "oil-gas", label: "Oil & Gas" },
  { value: "power-generation", label: "Power Generation" },
  { value: "renewables", label: "Renewables" },
  { value: "transmission", label: "Transmission" },
  { value: "distribution", label: "Distribution" },
  { value: "electricity-markets", label: "Electricity Markets" },
  { value: "new-energies", label: "New Energies" },
  { value: "energy-storage-systems", label: "Energy Storage Systems" },
  { value: "sustainability", label: "Sustainability" },
];

const subCommunityMap: Record<string, Array<{ value: string; label: string }>> = {
  "oil-gas": [
    { value: "upstream", label: "Upstream" },
    { value: "pipelines", label: "Pipelines" },
    { value: "refining", label: "Refining" },
    { value: "petrochemicals", label: "Petrochemicals" },
    { value: "cgd", label: "CGD" },
    { value: "lpg", label: "LPG" },
    { value: "retail", label: "Retail" },
    { value: "oil-markets", label: "Oil Markets" },
  ],
  "power-generation": [
    { value: "thermal", label: "Thermal" },
    { value: "nuclear", label: "Nuclear" },
  ],
  renewables: [
    { value: "solar", label: "Solar" },
    { value: "wind", label: "Wind" },
    { value: "hydro", label: "Hydro" },
  ],
};

/* =====================================================
   HELPERS
   ===================================================== */
function toggleInArray(arr: string[], value: string) {
  return arr.includes(value) ? arr.filter((x) => x !== value) : [...arr, value];
}

export function RegisterPage({ email, onRegistrationComplete }: RegisterPageProps) {
  const [isLoading, setIsLoading] = useState(false);

  const [formData, setFormData] = useState({
    firstName: "",
    lastName: "",
    country: "",
    state: "",
    jobTitle: "",
    organization: "",
    communities: [] as string[],
    subCommunities: [] as string[],
    industry: "",
    subIndustry: "",
  });

  /* =====================================================
     DERIVED DATA
     ===================================================== */
  const availableSubCommunities = useMemo(() => {
    const set = new Map<string, { value: string; label: string }>();
    formData.communities.forEach((c) =>
      (subCommunityMap[c] || []).forEach((s) => set.set(s.value, s))
    );
    return Array.from(set.values());
  }, [formData.communities]);

  /* =====================================================
     HANDLERS
     ===================================================== */
  const handleInputChange = (field: string, value: string) => {
    setFormData((prev) => ({ ...prev, [field]: value }));
  };

  const handleToggleCommunity = (value: string) => {
    setFormData((prev) => {
      const next = toggleInArray(prev.communities, value);
      return {
        ...prev,
        communities: next,
        subCommunities: prev.subCommunities.filter((s) =>
          (subCommunityMap[value] || []).some((x) => x.value === s)
        ),
      };
    });
  };

  const handleToggleSubCommunity = (value: string) => {
    setFormData((prev) => ({
      ...prev,
      subCommunities: toggleInArray(prev.subCommunities, value),
    }));
  };

  /* =====================================================
     SUBMIT
     ===================================================== */
  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsLoading(true);

    try {
      const primaryCommunity = formData.communities[0] || "";
      const primarySubCommunity = formData.subCommunities[0] || "";

      await AuthAPI.completeRegistration({
        first_name: formData.firstName,
        last_name: formData.lastName,
        email,

        job_title: formData.jobTitle,
        organization: formData.organization,
        country: formData.country,
        state: formData.state,

        // ✅ legacy (LABEL)
        community: communityLabelMap[primaryCommunity] || primaryCommunity,
        sub_community: primarySubCommunity,

        // ✅ source of truth (SLUG)
        communities: formData.communities,
        sub_communities: formData.subCommunities,

        industry: formData.industry,
        sub_industry: formData.subIndustry,
        privacy_accepted: true,
      });

      onRegistrationComplete();
    } catch (err: any) {
      alert(err?.message || "Registration failed");
    } finally {
      setIsLoading(false);
    }
  };

  const isFormValid =
    formData.firstName &&
    formData.lastName &&
    formData.jobTitle &&
    formData.organization &&
    formData.communities.length > 0;

  /* =====================================================
     UI
     ===================================================== */
  return (
    <div className="max-w-3xl mx-auto py-10">
      <Card>
        <CardHeader>
          <CardTitle>Create Your Profile</CardTitle>
        </CardHeader>

        <CardContent>
          <form onSubmit={handleSubmit} className="space-y-6">
            <Input
              placeholder="First Name"
              value={formData.firstName}
              onChange={(e) => handleInputChange("firstName", e.target.value)}
            />

            <Input
              placeholder="Last Name"
              value={formData.lastName}
              onChange={(e) => handleInputChange("lastName", e.target.value)}
            />

            <Input value={email} disabled />

            <Input
              placeholder="Job Title"
              value={formData.jobTitle}
              onChange={(e) => handleInputChange("jobTitle", e.target.value)}
            />

            <Input
              placeholder="Organization"
              value={formData.organization}
              onChange={(e) => handleInputChange("organization", e.target.value)}
            />

            {/* Communities */}
            <div>
              <Label>Communities *</Label>
              <div className="grid grid-cols-2 gap-2 border p-3 rounded-lg">
                {communities.map((c) => (
                  <label key={c.value} className="flex gap-2 items-center">
                    <Checkbox
                      checked={formData.communities.includes(c.value)}
                      onCheckedChange={() => handleToggleCommunity(c.value)}
                    />
                    {c.label}
                  </label>
                ))}
              </div>
            </div>

            {/* Sub Communities */}
            {availableSubCommunities.length > 0 && (
              <div>
                <Label>Sub Communities</Label>
                <div className="grid grid-cols-2 gap-2 border p-3 rounded-lg">
                  {availableSubCommunities.map((s) => (
                    <label key={s.value} className="flex gap-2 items-center">
                      <Checkbox
                        checked={formData.subCommunities.includes(s.value)}
                        onCheckedChange={() => handleToggleSubCommunity(s.value)}
                      />
                      {s.label}
                    </label>
                  ))}
                </div>
              </div>
            )}

            <Button disabled={!isFormValid || isLoading} className="w-full">
              {isLoading ? <Loader2 className="animate-spin" /> : "Complete Registration"}
            </Button>
          </form>
        </CardContent>
      </Card>
    </div>
  );
}
