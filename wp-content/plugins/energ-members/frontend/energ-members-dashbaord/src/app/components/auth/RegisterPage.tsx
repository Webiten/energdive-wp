import { useEffect, useMemo, useState } from "react";
import { Card, CardContent, CardHeader, CardTitle } from "../ui/card";
import { Button } from "../ui/button";
import { Input } from "../ui/input";
import { Label } from "../ui/label";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "../ui/select";
import { Checkbox } from "../ui/checkbox";
import { CheckCircle, Loader2, Phone, AlertCircle, RefreshCw, User, Briefcase, Building2, MapPin, Users, Layers, ChevronDown } from "lucide-react";
import { AuthAPI } from "@/app/lib/api";

interface RegisterPageProps {
  email: string;
  onRegistrationComplete: () => void;
}

// Country codes for mobile number
const countryCodes = [
  { value: "+1", label: "+1 (US/Canada)", flag: "🇺🇸" },
  { value: "+44", label: "+44 (UK)", flag: "🇬🇧" },
  { value: "+91", label: "+91 (India)", flag: "🇮🇳" },
  { value: "+49", label: "+49 (Germany)", flag: "🇩🇪" },
  { value: "+33", label: "+33 (France)", flag: "🇫🇷" },
  { value: "+86", label: "+86 (China)", flag: "🇨🇳" },
  { value: "+81", label: "+81 (Japan)", flag: "🇯🇵" },
  { value: "+61", label: "+61 (Australia)", flag: "🇦🇺" },
  { value: "+971", label: "+971 (UAE)", flag: "🇦🇪" },
  { value: "+65", label: "+65 (Singapore)", flag: "🇸🇬" },
];

// ===== Communities/Sub-Communities =====
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
    { value: "biopower", label: "Biopower" },
    { value: "cogeneration", label: "Cogeneration" },
    { value: "waste-to-energy", label: "Waste-to-Energy" },
  ],
  transmission: [{ value: "smart-grid", label: "Smart Grid" }],
  distribution: [
    { value: "smart-meters-ami", label: "Smart Meters & AMI" },
    { value: "ev-charging", label: "EV Charging" },
    { value: "data-centres", label: "Data Centres" },
    { value: "smart-cities", label: "Smart Cities" },
    { value: "railways-metros", label: "Railways & Metros" },
  ],
  "electricity-markets": [
    { value: "power-markets", label: "Power Markets" },
    { value: "carbon-markets", label: "Carbon Markets" },
    { value: "rco", label: "RCO" },
  ],
  "new-energies": [
    { value: "green-hydrogen", label: "Green Hydrogen" },
    { value: "e-fuels", label: "E-Fuels" },
  ],
  "energy-storage-systems": [
    { value: "bess", label: "BESS" },
    { value: "pumped-hydro", label: "Pumped Hydro" },
    { value: "caes", label: "CAES" },
    { value: "thermal", label: "Thermal" },
    { value: "flywheel", label: "Flywheel" },
  ],
  sustainability: [
    { value: "energy-efficiency", label: "Energy Efficiency" },
    { value: "occupational-health", label: "Occupational Health" },
    { value: "industrial-process-safety", label: "Industrial & Process Safety" },
    { value: "environment", label: "Environment" },
  ],
};

// ===== Industries =====
const industries = [
  { value: "agriculture", label: "Agriculture" },
  { value: "automobile", label: "Automobile" },
  { value: "aviation", label: "Aviation" },
  { value: "battery-storage", label: "Battery & Storage" },
  { value: "beauty-wellness", label: "Beauty & Wellness" },
  { value: "bfsi", label: "BFSI" },
  { value: "chemical", label: "Chemical" },
  { value: "construction-material", label: "Construction Material" },
  { value: "consulting", label: "Consulting" },
  { value: "consumer-durables", label: "Consumer Durables" },
  { value: "distribution", label: "Distribution" },
  { value: "e-commerce", label: "E-Commerce" },
  { value: "electrical", label: "Electrical" },
  { value: "electricity-markets", label: "Electricity Markets" },
  { value: "energy-efficiency-management", label: "Energy Efficiency Management" },
  { value: "engineering", label: "Engineering" },
  { value: "entertainment", label: "Entertainment" },
  { value: "environment", label: "Environment" },
  { value: "ev-charging", label: "EV Charging" },
  { value: "exporters-importers", label: "Exporters-Importers" },
  { value: "facility-management", label: "Facility Management" },
  { value: "fmcg", label: "FMCG" },
  { value: "gems", label: "Gems" },
  { value: "government", label: "Government" },
  { value: "healthcare", label: "Healthcare" },
  { value: "hotels", label: "Hotels" },
  { value: "it", label: "Information Technology (IT)" },
  { value: "infrastructure", label: "Infrastructure" },
  { value: "institutes-educational", label: "Institutes - Educational" },
  { value: "iron-steel", label: "Iron & Steel" },
  { value: "ites", label: "ITES" },
  { value: "leather", label: "Leather" },
  { value: "lighting", label: "Lighting" },
  { value: "logistics", label: "Logistics" },
  { value: "media", label: "Media" },
  { value: "mining", label: "Mining" },
  { value: "ngos", label: "NGOs" },
  { value: "office-automation", label: "Office Automation" },
  { value: "oil-gas", label: "Oil & Gas" },
  { value: "pharmaceuticals", label: "Pharmaceuticals" },
  { value: "power", label: "Power" },
  { value: "publishing", label: "Publishing" },
  { value: "railways", label: "Railways" },
  { value: "renewable", label: "Renewable" },
  { value: "retail", label: "Retail" },
  { value: "shipping", label: "Shipping" },
  { value: "sports", label: "Sports" },
  { value: "telecommunication", label: "Telecommunication" },
  { value: "textile", label: "Textile" },
  { value: "tourism", label: "Tourism" },
  { value: "transmission", label: "Transmission" },
  { value: "water-utility", label: "Water Utility" },
  { value: "wood", label: "Wood" },
];

/**
 * ✅ Sub-Industry map:
 * - If you already have your huge map, paste it here.
 * - Even if you keep this empty, we will show fallback options (so NEVER blank).
 */
const subIndustryMap: Record<string, Array<{ value: string; label: string }>> = {
  // Example (optional):
  // logistics: [
  //   { value: "road", label: "Road Logistics" },
  //   { value: "rail", label: "Rail Logistics" },
  //   { value: "marine", label: "Marine Logistics" },
  // ],
};

// ✅ fallback options (always available)
const COMMON_SUB_INDUSTRIES: Array<{ value: string; label: string }> = [
  { value: "operations", label: "Operations" },
  { value: "engineering", label: "Engineering" },
  { value: "projects", label: "Projects / EPC" },
  { value: "procurement", label: "Procurement" },
  { value: "supply-chain", label: "Supply Chain" },
  { value: "sales", label: "Sales / BD" },
  { value: "finance", label: "Finance" },
  { value: "legal", label: "Legal / Compliance" },
  { value: "digital", label: "Digital / IT" },
  { value: "hse", label: "HSE / Safety" },
];

const communityIndustryMap: Record<string, string[]> = {
  "oil-gas": ["oil-gas", "chemical", "engineering", "logistics", "mining", "infrastructure", "government", "consulting", "distribution", "shipping", "railways", "telecommunication", "it", "environment", "construction-material", "exporters-importers"],
  "power-generation": ["power", "electrical", "engineering", "construction-material", "infrastructure", "government", "consulting", "environment", "it", "logistics", "iron-steel", "mining", "consumer-durables"],
  renewables: ["renewable", "battery-storage", "electrical", "engineering", "construction-material", "infrastructure", "government", "consulting", "environment", "it", "logistics", "mining", "chemical", "ev-charging"],
  transmission: ["transmission", "electrical", "engineering", "infrastructure", "government", "consulting", "environment", "it", "telecommunication", "construction-material", "iron-steel", "logistics"],
  distribution: ["distribution", "electrical", "engineering", "it", "telecommunication", "infrastructure", "government", "consulting", "environment", "consumer-durables", "office-automation", "retail", "ev-charging", "logistics"],
  "electricity-markets": ["electricity-markets", "bfsi", "consulting", "government", "it", "telecommunication", "publishing", "media", "power", "renewable"],
  "new-energies": ["battery-storage", "renewable", "chemical", "engineering", "electrical", "oil-gas", "power", "consulting", "government", "it", "environment", "infrastructure", "logistics"],
  "energy-storage-systems": ["battery-storage", "power", "renewable", "electrical", "engineering", "chemical", "consulting", "government", "it", "environment", "infrastructure", "logistics"],
  sustainability: ["environment", "energy-efficiency-management", "consulting", "government", "it", "facility-management", "engineering", "construction-material", "power", "renewable", "chemical", "fmcg", "healthcare", "mining", "iron-steel", "textile"],
};

const countries = [
  { value: "usa", label: "United States" },
  { value: "uk", label: "United Kingdom" },
  { value: "india", label: "India" },
  { value: "germany", label: "Germany" },
  { value: "france", label: "France" },
];

function phoneIdentifierDigits(countryCode: string, mobile: string) {
  const cleanedMobile = (mobile || "").replace(/[^\d]/g, "");
  const ccDigits = (countryCode || "").replace(/[^\d]/g, "");
  return `${ccDigits}${cleanedMobile}`.replace(/[^\d]/g, "");
}

async function requestOtpWithContext(identifier: string, context: "login" | "register_phone") {
  try {
    // @ts-ignore
    return await AuthAPI.requestOtp(identifier, context);
  } catch {
    const BASE = "/wp-json/energ/v1";
    const res = await fetch(`${BASE}/auth/request-otp`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ identifier, context }),
    });
    const data = await res.json();
    if (!res.ok) throw data;
    return data;
  }
}

function toggleInArray(arr: string[], value: string) {
  return arr.includes(value) ? arr.filter((x) => x !== value) : [...arr, value];
}

// Section Header Component
function SectionHeader({ icon: Icon, title, subtitle }: { icon: React.ElementType; title: string; subtitle?: string }) {
  return (
    <div className="flex items-start gap-4 mb-6">
      <div className="flex-shrink-0 w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-lg shadow-emerald-500/20">
        <Icon className="w-5 h-5 text-white" />
      </div>
      <div>
        <h3 className="text-lg font-semibold text-gray-900">{title}</h3>
        {subtitle && <p className="text-sm text-gray-500 mt-0.5">{subtitle}</p>}
      </div>
    </div>
  );
}

// Form Field Component
function FormField({ label, required, children, error }: { label: string; required?: boolean; children: React.ReactNode; error?: string }) {
  return (
    <div className="space-y-2">
      <Label className="text-sm font-medium text-gray-700">
        {label}
        {required && <span className="text-red-500 ml-0.5">*</span>}
      </Label>
      {children}
      {error && <p className="text-xs text-red-500 mt-1">{error}</p>}
    </div>
  );
}

export function RegisterPage({ email, onRegistrationComplete }: RegisterPageProps) {
  const [isLoading, setIsLoading] = useState(false);

  const [formData, setFormData] = useState({
    firstName: "",
    lastName: "",
    countryCode: "+1",
    mobile: "",
    country: "",
    state: "",

    jobTitle: "",
    organization: "",

    communities: [] as string[],
    subCommunities: [] as string[],

    industry: "",
    subIndustry: "",
    areaOfIndustry: "",
  });


  const [otpState, setOtpState] = useState<"idle" | "sent" | "verifying" | "verified" | "error">("idle");
  const [otp, setOtp] = useState(["", "", "", "", "", ""]);
  const [otpError, setOtpError] = useState("");
  const [resendTimer, setResendTimer] = useState(0);

  // ✅ stable map
  const safeSubIndustryMap = useMemo(() => subIndustryMap || {}, []);

  // ✅ sub-communities based on selected communities (union)
  const availableSubCommunities = useMemo(() => {
    const set = new Map<string, { value: string; label: string }>();
    for (const c of formData.communities) {
      for (const s of subCommunityMap[c] || []) set.set(s.value, s);
    }
    return Array.from(set.values());
  }, [formData.communities]);

  // ✅ industry based on selected communities (union)
  const filteredIndustries = useMemo(() => {
    if (!formData.communities.length) return industries;

    const allowed = new Set<string>();
    formData.communities.forEach((c) => (communityIndustryMap[c] || []).forEach((x) => allowed.add(x)));
    return industries.filter((i) => allowed.has(i.value));
  }, [formData.communities]);

  // ✅ sub-industry options never blank
  const subIndustryOptions = useMemo(() => {
    const custom = safeSubIndustryMap[formData.industry];
    if (custom && custom.length) return custom;
    // fallback options always
    return COMMON_SUB_INDUSTRIES;
  }, [formData.industry, safeSubIndustryMap]);

  useEffect(() => {
    if (resendTimer > 0) {
      const timer = setTimeout(() => setResendTimer(resendTimer - 1), 1000);
      return () => clearTimeout(timer);
    }
  }, [resendTimer]);

  const handleInputChange = (field: string, value: string) => {
    setFormData((prev) => {
      const updated: any = { ...prev, [field]: value };
      if (field === "industry") {
        // reset sub-industry safely
        updated.subIndustry = "";
      }
      return updated;
    });
  };

  // ✅ when mobile changes after verified => reset OTP state (important)
  const handleMobileChange = (value: string) => {
    setFormData((prev) => ({ ...prev, mobile: value }));
    if (otpState === "verified") {
      setOtpState("idle");
      setOtp(["", "", "", "", "", ""]);
      setOtpError("");
      setResendTimer(0);
    }
  };

  const handleToggleCommunity = (value: string) => {
    setFormData((prev) => {
      const nextCommunities = toggleInArray(prev.communities, value);

      // if none selected -> hard reset dependent fields
      if (!nextCommunities.length) {
        return {
          ...prev,
          communities: [],
          subCommunities: [],
          industry: "",
          subIndustry: "",
          areaOfIndustry: "",
        };
      }

      // remove invalid sub-communities
      const validSubs = new Set<string>();
      nextCommunities.forEach((c) => (subCommunityMap[c] || []).forEach((s) => validSubs.add(s.value)));
      const nextSubCommunities = prev.subCommunities.filter((s) => validSubs.has(s));

      // allowed industries union
      const nextAllowedIndustries = new Set<string>();
      nextCommunities.forEach((c) => (communityIndustryMap[c] || []).forEach((x) => nextAllowedIndustries.add(x)));

      // if current industry not allowed -> clear
      const nextIndustry = prev.industry && nextAllowedIndustries.has(prev.industry) ? prev.industry : "";
      const nextSubIndustry = nextIndustry ? prev.subIndustry : "";

      return {
        ...prev,
        communities: nextCommunities,
        subCommunities: nextSubCommunities,
        industry: nextIndustry,
        subIndustry: nextSubIndustry,
        areaOfIndustry: "",
      };
    });
  };

  const handleToggleSubCommunity = (value: string) => {
    setFormData((prev) => ({
      ...prev,
      subCommunities: toggleInArray(prev.subCommunities, value),
      areaOfIndustry: "",
    }));
  };

  const handleSendOTP = async () => {
    const digitsMobile = (formData.mobile || "").replace(/[^\d]/g, "");
    if (!digitsMobile || digitsMobile.length < 8) {
      setOtpError("Please enter a valid mobile number");
      return;
    }

    setOtpState("sent");
    setResendTimer(60);
    setOtpError("");

    try {
      const identifier = phoneIdentifierDigits(formData.countryCode, formData.mobile);
      await requestOtpWithContext(identifier, "register_phone");
    } catch (err: any) {
      setOtpState("error");
      setOtpError(err?.message || err?.data?.message || "Failed to send OTP. Please try again.");
      setResendTimer(0);
    }
  };

  const handleResendOTP = async () => {
    if (resendTimer > 0) return;
    await handleSendOTP();
  };

  const handleOTPChange = (index: number, value: string) => {
    if (value.length > 1) return;
    const newOtp = [...otp];
    newOtp[index] = value;
    setOtp(newOtp);
    setOtpError("");

    if (value && index < 5) document.getElementById(`otp-${index + 1}`)?.focus();
    if (newOtp.every((d) => d !== "") && index === 5) void verifyOTP(newOtp.join(""));
  };

  const handleOTPKeyDown = (index: number, e: React.KeyboardEvent<HTMLInputElement>) => {
    if (e.key === "Backspace" && !otp[index] && index > 0) document.getElementById(`otp-${index - 1}`)?.focus();
  };

  const verifyOTP = async (otpValue: string) => {
    setOtpState("verifying");
    try {
      const identifier = phoneIdentifierDigits(formData.countryCode, formData.mobile);
      const res = await AuthAPI.verifyOtp(identifier, otpValue);
      if (res?.success !== true) throw new Error(res?.message || "Invalid OTP. Please try again.");
      setOtpState("verified");
      setOtpError("");
    } catch (err: any) {
      const message = err?.message || err?.data?.message || "Invalid OTP. Please try again.";
      setOtpState("error");
      setOtpError(message);
      setTimeout(() => setOtpState("sent"), 1500);
    }
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();

    if (otpState !== "verified") {
      setOtpError("Please verify your mobile number before submitting");
      return;
    }

    setIsLoading(true);
    setOtpError("");

    try {
      const identifier = phoneIdentifierDigits(formData.countryCode, formData.mobile);

      // backward compatible
      const primaryCommunity = formData.communities[0] || "";
      const primarySubCommunity = formData.subCommunities[0] || "";

      await AuthAPI.completeRegistration({
        first_name: formData.firstName,
        last_name: formData.lastName,
        email,
        phone: identifier,
        country: formData.country,
        state: formData.state,

        job_title: formData.jobTitle,
        organization: formData.organization,


        // ✅ old fields (so your existing backend validation/DB works)
        community: primaryCommunity,
        sub_community: primarySubCommunity,

        // ✅ new fields (backend upgrade later)
        communities: formData.communities,
        sub_communities: formData.subCommunities,

        industry: formData.industry,
        sub_industry: formData.subIndustry,
        area_of_industry: formData.areaOfIndustry,
        privacy_accepted: true,
      });

      localStorage.removeItem("onboarding_required");
      setIsLoading(false);
      onRegistrationComplete();
    } catch (err: any) {
      setIsLoading(false);
      setOtpError(err?.message || err?.data?.message || "Registration failed. Please try again.");
    }
  };

  const isFormValid =
    formData.firstName &&
    formData.lastName &&
    formData.mobile &&
    formData.country &&
    formData.jobTitle &&
    formData.organization &&
    formData.communities.length > 0 &&
    formData.industry &&
    otpState === "verified";

  // Progress calculation
  const calculateProgress = () => {
    let progress = 0;
    const fields = [
      formData.firstName,
      formData.lastName,
      formData.mobile,
      formData.country,
      formData.jobTitle,
      formData.organization,
      formData.communities.length > 0,
      formData.industry,
      otpState === "verified",
    ];
    progress = (fields.filter(Boolean).length / fields.length) * 100;
    return Math.round(progress);
  };

  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-50 via-gray-50 to-emerald-50/30 py-8 px-4">
      <div className="max-w-4xl mx-auto">
        {/* Header */}
        <div className="text-center mb-8">
          <div className="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-xl shadow-emerald-500/25 mb-4">
            <User className="w-8 h-8 text-white" />
          </div>
          <h1 className="text-3xl font-bold text-gray-900 mb-2">Create Your Account</h1>
          <p className="text-gray-600 max-w-md mx-auto">
            Join our professional energy community and connect with industry experts worldwide
          </p>
          
          {/* Progress Bar */}
          <div className="mt-6 max-w-xs mx-auto">
            <div className="flex justify-between text-xs text-gray-500 mb-2">
              <span>Profile Completion</span>
              <span className="font-medium text-emerald-600">{calculateProgress()}%</span>
            </div>
            <div className="h-2 bg-gray-200 rounded-full overflow-hidden">
              <div 
                className="h-full bg-gradient-to-r from-emerald-500 to-teal-500 transition-all duration-500 ease-out"
                style={{ width: `${calculateProgress()}%` }}
              />
            </div>
          </div>
        </div>

        <form onSubmit={handleSubmit} className="space-y-6">
          {/* Personal Information Card */}
          <Card className="shadow-xl border-0 overflow-hidden bg-white/80 backdrop-blur-sm">
            <div className="h-1 bg-gradient-to-r from-emerald-500 via-teal-500 to-cyan-500" />
            <CardHeader className="pb-4">
              <SectionHeader 
                icon={User} 
                title="Personal Information" 
                subtitle="Tell us about yourself"
              />
            </CardHeader>
            <CardContent className="space-y-6">
              {/* Name Row */}
              <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
                <FormField label="First Name" required>
                  <Input
                    value={formData.firstName}
                    onChange={(e) => handleInputChange("firstName", e.target.value)}
                    placeholder="e.g. John"
                    className="h-11 border-gray-200 focus:border-emerald-500 focus:ring-emerald-500/20 transition-all"
                  />
                </FormField>

                <FormField label="Last Name" required>
                  <Input
                    value={formData.lastName}
                    onChange={(e) => handleInputChange("lastName", e.target.value)}
                    placeholder="e.g. Doe"
                    className="h-11 border-gray-200 focus:border-emerald-500 focus:ring-emerald-500/20 transition-all"
                  />
                </FormField>
              </div>

              {/* Email */}
              <FormField label="Email Address">
                <div className="relative">
                  <Input
                    type="email"
                    value={email}
                    disabled
                    className="h-11 bg-gray-50 border-gray-200 text-gray-600 pr-10"
                  />
                  <div className="absolute right-3 top-1/2 -translate-y-1/2">
                    <div className="w-5 h-5 rounded-full bg-emerald-100 flex items-center justify-center">
                      <CheckCircle className="w-3 h-3 text-emerald-600" />
                    </div>
                  </div>
                </div>
                <p className="text-xs text-gray-500 mt-1.5 flex items-center gap-1">
                  <CheckCircle className="w-3 h-3 text-emerald-500" />
                  This email has been verified
                </p>
              </FormField>

              {/* Mobile with OTP */}
              <div className="space-y-3">
                <FormField label="Mobile Number" required error={otpState === "error" ? otpError : undefined}>
                  <div className="flex gap-3">
                    {/* Country Code Select */}
                    <div className="relative flex-shrink-0">
                      <select
                        value={formData.countryCode}
                        onChange={(e) => handleInputChange("countryCode", e.target.value)}
                        disabled={otpState === "verified"}
                        className="h-11 pl-3 pr-10 border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 disabled:bg-gray-50 disabled:text-gray-500 appearance-none cursor-pointer transition-all hover:border-gray-300"
                      >
                        {countryCodes.map((code) => (
                          <option key={code.value} value={code.value}>
                            {code.flag} {code.value}
                          </option>
                        ))}
                      </select>
                      <ChevronDown className="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
                    </div>

                    {/* Mobile Input */}
                    <div className="flex-1 flex gap-3">
                      <Input
                        type="tel"
                        value={formData.mobile}
                        onChange={(e) => handleMobileChange(e.target.value)}
                        placeholder="555 123 4567"
                        disabled={otpState === "verified"}
                        className={`h-11 flex-1 border-gray-200 focus:border-emerald-500 focus:ring-emerald-500/20 transition-all ${otpState === "verified" ? "bg-gray-50" : ""}`}
                      />

                      {/* OTP Button / Verified Badge */}
                      {otpState === "idle" || otpState === "error" ? (
                        <Button
                          type="button"
                          onClick={() => void handleSendOTP()}
                          className="h-11 px-5 bg-emerald-600 hover:bg-emerald-700 text-white shadow-lg shadow-emerald-500/25 transition-all"
                        >
                          <Phone className="w-4 h-4 mr-2" />
                          Send OTP
                        </Button>
                      ) : otpState === "verified" ? (
                        <div className="h-11 px-4 flex items-center gap-2 bg-emerald-50 border border-emerald-200 rounded-lg">
                          <div className="w-5 h-5 rounded-full bg-emerald-500 flex items-center justify-center">
                            <CheckCircle className="w-3 h-3 text-white" />
                          </div>
                          <span className="text-sm font-medium text-emerald-700">Verified</span>
                        </div>
                      ) : null}
                    </div>
                  </div>
                </FormField>

                {/* OTP Verification Panel */}
                {(otpState === "sent" || otpState === "verifying" || otpState === "error") && (
                  <div className="mt-4 p-5 bg-gradient-to-br from-gray-50 to-slate-50 border border-gray-200 rounded-xl space-y-4">
                    <div className="flex items-center justify-between">
                      <div>
                        <p className="text-sm font-semibold text-gray-900">Enter Verification Code</p>
                        <p className="text-xs text-gray-500 mt-0.5">
                          We sent a 6-digit code to <span className="font-medium text-gray-700">{formData.countryCode} {formData.mobile}</span>
                        </p>
                      </div>
                      {otpState === "verifying" && (
                        <div className="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center">
                          <Loader2 className="w-4 h-4 text-emerald-600 animate-spin" />
                        </div>
                      )}
                    </div>

                    {/* OTP Inputs */}
                    <div className="flex gap-2 justify-center">
                      {otp.map((digit, index) => (
                        <Input
                          key={index}
                          id={`otp-${index}`}
                          type="text"
                          inputMode="numeric"
                          maxLength={1}
                          value={digit}
                          onChange={(e) => handleOTPChange(index, e.target.value.replace(/[^0-9]/g, ""))}
                          onKeyDown={(e) => handleOTPKeyDown(index, e)}
                          disabled={otpState === "verifying"}
                          className={`w-12 h-12 text-center text-xl font-bold rounded-lg border-2 transition-all ${
                            otpState === "error" 
                              ? "border-red-400 bg-red-50 text-red-600 focus:border-red-500 focus:ring-red-500/20" 
                              : digit 
                                ? "border-emerald-500 bg-emerald-50 text-emerald-700" 
                                : "border-gray-200 focus:border-emerald-500 focus:ring-emerald-500/20"
                          }`}
                        />
                      ))}
                    </div>

                    {/* Error Message */}
                    {otpError && otpState === "error" && (
                      <div className="flex items-center justify-center gap-2 text-red-600 text-sm bg-red-50 py-2 px-4 rounded-lg">
                        <AlertCircle className="w-4 h-4" />
                        <span>{otpError}</span>
                      </div>
                    )}

                    {/* Resend */}
                    <div className="text-center pt-2">
                      {resendTimer > 0 ? (
                        <p className="text-sm text-gray-500">
                          Resend code in <span className="font-semibold text-emerald-600">{resendTimer}s</span>
                        </p>
                      ) : (
                        <button
                          type="button"
                          onClick={() => void handleResendOTP()}
                          className="inline-flex items-center gap-2 text-sm font-medium text-emerald-600 hover:text-emerald-700 transition-colors"
                        >
                          <RefreshCw className="w-4 h-4" />
                          Resend OTP
                        </button>
                      )}
                    </div>
                  </div>
                )}
              </div>

              {/* Location Row */}
              <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
                <FormField label="Country" required>
                  <Select value={formData.country} onValueChange={(value) => handleInputChange("country", value)}>
                    <SelectTrigger className="h-11 border-gray-200 focus:ring-emerald-500/20 focus:border-emerald-500">
                      <SelectValue placeholder="Select your country" />
                    </SelectTrigger>
                    <SelectContent className="max-h-60">
                      {countries.map((c) => (
                        <SelectItem key={c.value} value={c.value}>
                          {c.label}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </FormField>

                <FormField label="State / Province">
                  <Input
                    value={formData.state}
                    onChange={(e) => handleInputChange("state", e.target.value)}
                    placeholder="e.g. California"
                    className="h-11 border-gray-200 focus:border-emerald-500 focus:ring-emerald-500/20 transition-all"
                  />
                </FormField>
              </div>
            </CardContent>
          </Card>

          {/* Professional Information Card */}
          <Card className="shadow-xl border-0 overflow-hidden bg-white/80 backdrop-blur-sm">
            <div className="h-1 bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500" />
            <CardHeader className="pb-4">
              <SectionHeader 
                icon={Briefcase} 
                title="Professional Information" 
                subtitle="Your work details"
              />
            </CardHeader>
            <CardContent className="space-y-6">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
                <FormField label="Job Title" required>
                  <Input
                    value={formData.jobTitle}
                    onChange={(e) => handleInputChange("jobTitle", e.target.value)}
                    placeholder="e.g. Senior Engineer"
                    className="h-11 border-gray-200 focus:border-emerald-500 focus:ring-emerald-500/20 transition-all"
                  />
                </FormField>

                <FormField label="Organization" required>
                  <div className="relative">
                    <Building2 className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                    <Input
                      value={formData.organization}
                      onChange={(e) => handleInputChange("organization", e.target.value)}
                      placeholder="Company / Organization Name"
                      className="h-11 pl-10 border-gray-200 focus:border-emerald-500 focus:ring-emerald-500/20 transition-all"
                    />
                  </div>
                </FormField>
              </div>
            </CardContent>
          </Card>

          {/* Communities & Classification Card */}
          <Card className="shadow-xl border-0 overflow-hidden bg-white/80 backdrop-blur-sm">
            <div className="h-1 bg-gradient-to-r from-orange-500 via-amber-500 to-yellow-500" />
            <CardHeader className="pb-4">
              <SectionHeader 
                icon={Users} 
                title="Communities & Classification" 
                subtitle="Select your areas of interest"
              />
            </CardHeader>
            <CardContent className="space-y-8">
              {/* Communities */}
              <div className="space-y-3">
                <FormField label="Communities" required>
                  <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    {communities.map((comm) => {
                      const checked = formData.communities.includes(comm.value);
                      return (
                        <label
                          key={comm.value}
                          className={`flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all ${
                            checked 
                              ? "border-emerald-500 bg-emerald-50/50" 
                              : "border-gray-100 bg-white hover:border-gray-200 hover:bg-gray-50"
                          }`}
                        >
                          <Checkbox
                            checked={checked}
                            onCheckedChange={(v) => {
                              if (v === "indeterminate") return;
                              if (v !== checked) handleToggleCommunity(comm.value);
                            }}
                            className="data-[state=checked]:bg-emerald-500 data-[state=checked]:border-emerald-500"
                          />
                          <span className={`text-sm font-medium ${checked ? "text-emerald-900" : "text-gray-700"}`}>
                            {comm.label}
                          </span>
                        </label>
                      );
                    })}
                  </div>
                </FormField>

                {/* Selected Communities Tags */}
                {formData.communities.length > 0 && (
                  <div className="flex flex-wrap gap-2 pt-2">
                    {formData.communities.map((commValue) => {
                      const comm = communities.find(c => c.value === commValue);
                      return (
                        <span 
                          key={commValue}
                          className="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-100 text-emerald-800 text-xs font-medium rounded-full"
                        >
                          {comm?.label}
                          <button
                            type="button"
                            onClick={() => handleToggleCommunity(commValue)}
                            className="w-4 h-4 rounded-full bg-emerald-200 hover:bg-emerald-300 flex items-center justify-center transition-colors"
                          >
                            <span className="text-emerald-700 text-xs">×</span>
                          </button>
                        </span>
                      );
                    })}
                  </div>
                )}
              </div>

              {/* Sub-Communities */}
              {formData.communities.length > 0 && (
                <div className="space-y-3 pt-4 border-t border-gray-100">
                  <FormField label="Sub-Communities">
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                      {availableSubCommunities.length ? (
                        availableSubCommunities.map((sub) => {
                          const checked = formData.subCommunities.includes(sub.value);
                          return (
                            <label
                              key={sub.value}
                              className={`flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all ${
                                checked 
                                  ? "border-teal-500 bg-teal-50/50" 
                                  : "border-gray-100 bg-white hover:border-gray-200 hover:bg-gray-50"
                              }`}
                            >
                              <Checkbox
                                checked={checked}
                                onCheckedChange={(v) => {
                                  if (v === "indeterminate") return;
                                  if (v !== checked) handleToggleSubCommunity(sub.value);
                                }}
                                className="data-[state=checked]:bg-teal-500 data-[state=checked]:border-teal-500"
                              />
                              <span className={`text-sm font-medium ${checked ? "text-teal-900" : "text-gray-700"}`}>
                                {sub.label}
                              </span>
                            </label>
                          );
                        })
                      ) : (
                        <p className="text-sm text-gray-500 col-span-full py-4 text-center bg-gray-50 rounded-xl">
                          No sub-communities available for selected communities
                        </p>
                      )}
                    </div>
                  </FormField>

                  {/* Selected Sub-Communities Tags */}
                  {formData.subCommunities.length > 0 && (
                    <div className="flex flex-wrap gap-2 pt-2">
                      {formData.subCommunities.map((subValue) => {
                        const sub = availableSubCommunities.find(s => s.value === subValue);
                        return (
                          <span 
                            key={subValue}
                            className="inline-flex items-center gap-1.5 px-3 py-1.5 bg-teal-100 text-teal-800 text-xs font-medium rounded-full"
                          >
                            {sub?.label}
                            <button
                              type="button"
                              onClick={() => handleToggleSubCommunity(subValue)}
                              className="w-4 h-4 rounded-full bg-teal-200 hover:bg-teal-300 flex items-center justify-center transition-colors"
                            >
                              <span className="text-teal-700 text-xs">×</span>
                            </button>
                          </span>
                        );
                      })}
                    </div>
                  )}
                </div>
              )}

              {/* Industry & Sub-Industry */}
              {formData.communities.length > 0 && (
                <div className="grid grid-cols-1 md:grid-cols-2 gap-5 pt-4 border-t border-gray-100">
                  <FormField label="Industry" required>
                    <Select
                      value={formData.industry}
                      onValueChange={(value) => handleInputChange("industry", value)}
                    >
                      <SelectTrigger className="h-11 border-gray-200 focus:ring-emerald-500/20 focus:border-emerald-500">
                        <SelectValue placeholder="Select your industry" />
                      </SelectTrigger>
                      <SelectContent className="max-h-72">
                        {filteredIndustries.map((ind) => (
                          <SelectItem key={ind.value} value={ind.value}>
                            {ind.label}
                          </SelectItem>
                        ))}
                      </SelectContent>
                    </Select>
                  </FormField>

                  <FormField label="Sub-Industry">
                    <Select
                      value={formData.subIndustry}
                      onValueChange={(value) => handleInputChange("subIndustry", value)}
                      disabled={!formData.industry}
                    >
                      <SelectTrigger className={`h-11 border-gray-200 focus:ring-emerald-500/20 focus:border-emerald-500 ${!formData.industry ? "bg-gray-50" : ""}`}>
                        <SelectValue placeholder={!formData.industry ? "Select industry first" : "Select sub-industry"} />
                      </SelectTrigger>
                      <SelectContent className="max-h-72">
                        {subIndustryOptions.map((sub) => (
                          <SelectItem key={sub.value} value={sub.value}>
                            {sub.label}
                          </SelectItem>
                        ))}
                      </SelectContent>
                    </Select>
                    {!safeSubIndustryMap[formData.industry]?.length && formData.industry && (
                      <p className="text-xs text-gray-400 mt-1.5 italic">
                        Showing common options for {industries.find(i => i.value === formData.industry)?.label}
                      </p>
                    )}
                  </FormField>
                </div>
              )}
            </CardContent>
          </Card>

          {/* Verification Warning */}
          {otpState !== "verified" && formData.mobile && (
            <div className="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-start gap-3">
              <div className="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0">
                <AlertCircle className="w-4 h-4 text-amber-600" />
              </div>
              <div>
                <p className="text-sm font-semibold text-amber-900">Mobile Verification Required</p>
                <p className="text-sm text-amber-800 mt-0.5">
                  Please verify your mobile number to complete registration.
                </p>
              </div>
            </div>
          )}

          {/* Submit Button */}
          <div className="pt-4">
            <Button
              type="submit"
              disabled={isLoading || !isFormValid}
              className={`w-full h-14 text-base font-semibold rounded-xl transition-all ${
                isFormValid 
                  ? "bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 shadow-xl shadow-emerald-500/25 hover:shadow-emerald-500/40 hover:-translate-y-0.5" 
                  : "bg-gray-300 cursor-not-allowed"
              }`}
            >
              {isLoading ? (
                <span className="flex items-center justify-center gap-2">
                  <Loader2 className="w-5 h-5 animate-spin" />
                  Creating your profile...
                </span>
              ) : (
                <span className="flex items-center justify-center gap-2">
                  <CheckCircle className="w-5 h-5" />
                  Complete Registration
                </span>
              )}
            </Button>

            {/* Form Error */}
            {otpError && otpState !== "error" && (
              <div className="mt-4 flex items-center justify-center gap-2 text-red-600 text-sm bg-red-50 py-3 px-4 rounded-xl">
                <AlertCircle className="w-4 h-4" />
                <span>{otpError}</span>
              </div>
            )}
          </div>

          {/* Footer */}
          <p className="text-center text-xs text-gray-500">
            By completing registration, you agree to our Terms of Service and Privacy Policy
          </p>
        </form>
      </div>
    </div>
  );
}