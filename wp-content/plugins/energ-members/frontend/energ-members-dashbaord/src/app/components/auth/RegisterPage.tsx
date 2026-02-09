import { useEffect, useMemo, useState } from "react";
import { Card, CardContent, CardHeader, CardTitle } from "../ui/card";
import { Button } from "../ui/button";
import { Input } from "../ui/input";
import { Label } from "../ui/label";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "../ui/select";
import { Checkbox } from "../ui/checkbox";
import { CheckCircle, Loader2, Phone, AlertCircle, RefreshCw } from "lucide-react";
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


  return (
    <div className="min-h-screen flex items-center justify-center bg-white">
      <div className="max-w-3xl mx-auto">
        <div className="text-center mb-8">
          <div className="inline-flex items-center gap-3 mb-4">
            {/* <div className="w-12 h-12 bg-emerald-600 rounded-lg flex items-center justify-center">
              <span className="text-white font-bold text-xl">E</span>
            </div> */}
            {/* <div className="text-left">
              <h1 className="text-2xl font-bold text-gray-900">ENERGCLUB</h1>
              <p className="text-sm text-gray-600">Energy Intelligence Platform</p>
            </div> */}
          </div>
        </div>

        <Card className="shadow-xl">
          <CardHeader>
            <CardTitle className="text-2xl text-center">Create Your Profile</CardTitle>
            <p className="text-center text-gray-600 text-sm mt-2">
              Complete your registration to access the platform
            </p>
          </CardHeader>

          <CardContent>
            <form onSubmit={handleSubmit} className="space-y-6">
              {/* Personal Information */}
              <div className="space-y-4">
                <h3 className="text-lg font-semibold text-gray-900 border-b pb-2">Personal Information</h3>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div className="space-y-2">
                    <Label htmlFor="firstName">First Name *</Label>
                    <Input
                      id="firstName"
                      value={formData.firstName}
                      onChange={(e) => handleInputChange("firstName", e.target.value)}
                      placeholder="John"
                      required
                    />
                  </div>

                  <div className="space-y-2">
                    <Label htmlFor="lastName">Last Name *</Label>
                    <Input
                      id="lastName"
                      value={formData.lastName}
                      onChange={(e) => handleInputChange("lastName", e.target.value)}
                      placeholder="Doe"
                      required
                    />
                  </div>
                </div>

                <div className="space-y-2">
                  <Label htmlFor="email">Email Address</Label>
                  <Input id="email" type="email" value={email} disabled className="bg-gray-50" />
                  <p className="text-xs text-gray-500">This email has been verified and cannot be changed</p>
                </div>

                {/* Mobile OTP */}
                <div className="space-y-3">
                  <Label htmlFor="mobile">Mobile Number *</Label>

                  <div className="flex gap-2">
                    <select
                      value={formData.countryCode}
                      onChange={(e) => handleInputChange("countryCode", e.target.value)}
                      disabled={otpState === "verified"}
                      className={`
    px-3 py-2.5 border border-gray-300 rounded-lg
    focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent
    bg-white cursor-pointer
    disabled:bg-gray-50 disabled:cursor-not-allowed
    appearance-none
    shadow-sm hover:border-gray-400
    transition-colors
  `}
                      style={{
                        backgroundImage: `url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E")`,
                        backgroundRepeat: 'no-repeat',
                        backgroundPosition: 'right 0.5rem center',
                        backgroundSize: '1.5em 1.5em',
                        paddingRight: '2.5rem'
                      }}
                    >
                      {countryCodes.map((code) => (
                        <option key={code.value} value={code.value} className="py-2">
                          {code.flag} {code.value}
                        </option>
                      ))}
                    </select>

                    <div className="flex-1 flex gap-2">
                      <Input
                        id="mobile"
                        type="tel"
                        value={formData.mobile}
                        onChange={(e) => handleMobileChange(e.target.value)}
                        placeholder="555 123 4567"
                        disabled={otpState === "verified"}
                        className={otpState === "verified" ? "bg-gray-50" : ""}
                        required
                      />

                      {otpState === "idle" || otpState === "error" ? (
                        <Button
                          type="button"
                          onClick={() => void handleSendOTP()}
                          className="bg-emerald-600 hover:bg-emerald-700 whitespace-nowrap"
                        >
                          <Phone className="w-4 h-4 mr-2" />
                          Send OTP
                        </Button>
                      ) : otpState === "verified" ? (
                        <div className="flex items-center gap-2 px-4 py-2 bg-green-50 border border-green-200 rounded-md">
                          <CheckCircle className="w-4 h-4 text-green-600" />
                          <span className="text-sm text-green-700 font-medium">Verified</span>
                        </div>
                      ) : null}
                    </div>
                  </div>

                  {(otpState === "sent" || otpState === "verifying" || otpState === "error") && (
                    <div className="mt-4 p-4 bg-gray-50 border border-gray-200 rounded-lg space-y-4">
                      <div className="flex items-start gap-2">
                        <div className="flex-1">
                          <p className="text-sm font-medium text-gray-900">Enter Verification Code</p>
                          <p className="text-xs text-gray-600 mt-1">
                            We sent a 6-digit code to {formData.countryCode} {formData.mobile}
                          </p>
                        </div>
                        {otpState === "verifying" && <Loader2 className="w-5 h-5 text-emerald-600 animate-spin" />}
                      </div>

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
                            className={`w-12 h-12 text-center text-lg font-semibold ${otpState === "error" ? "border-red-500" : ""}`}
                            disabled={otpState === "verifying"}
                          />
                        ))}
                      </div>

                      {otpError && (
                        <div className="flex items-center gap-2 text-red-600 text-sm">
                          <AlertCircle className="w-4 h-4" />
                          <span>{otpError}</span>
                        </div>
                      )}

                      <div className="text-center">
                        {resendTimer > 0 ? (
                          <p className="text-sm text-gray-600">
                            Resend code in <span className="font-semibold text-gray-900">{resendTimer}s</span>
                          </p>
                        ) : (
                          <Button
                            type="button"
                            variant="outline"
                            size="sm"
                            onClick={() => void handleResendOTP()}
                            className="text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50"
                          >
                            <RefreshCw className="w-4 h-4 mr-2" />
                            Resend OTP
                          </Button>
                        )}
                      </div>
                    </div>
                  )}
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div className="space-y-2">
                    <Label htmlFor="country">Country *</Label>
                    <Select value={formData.country} onValueChange={(value) => handleInputChange("country", value)}>
                      <SelectTrigger>
                        <SelectValue placeholder="Select country" />
                      </SelectTrigger>
                      <SelectContent>
                        {countries.map((c) => (
                          <SelectItem key={c.value} value={c.value}>
                            {c.label}
                          </SelectItem>
                        ))}
                      </SelectContent>
                    </Select>
                  </div>

                  <div className="space-y-2">
                    <Label htmlFor="state">State / Province</Label>
                    <Input
                      id="state"
                      value={formData.state}
                      onChange={(e) => handleInputChange("state", e.target.value)}
                      placeholder="Enter state or province"
                    />
                  </div>
                </div>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div className="space-y-2">
                  <Label htmlFor="jobTitle">Job Title</Label>
                  <Input
                    id="jobTitle"
                    value={formData.jobTitle}
                    onChange={(e) => handleInputChange("jobTitle", e.target.value)}
                    placeholder="e.g. Senior Engineer"
                  />
                </div>

                <div className="space-y-2">
                  <Label htmlFor="Organization">Organization</Label>
                  <Input
                    id="organization"
                    value={formData.organization}
                    onChange={(e) => handleInputChange("organization", e.target.value)}
                    placeholder="Company / Organization Name"
                  />

                </div>
              </div>


              {/* Professional Classification */}
              <div className="space-y-4">
                <h3 className="text-lg font-semibold text-gray-900 border-b pb-2">Choose Communities and Sub Communities</h3>

                {/* Community Multi-Select */}
                <div className="space-y-2">
                  <Label>Communities *</Label>

                  <div className="grid grid-cols-1 md:grid-cols-2 gap-2 rounded-lg border p-3">
                    {communities.map((comm) => {
                      const checked = formData.communities.includes(comm.value);
                      return (
                        <label
                          key={comm.value}
                          className="flex items-center gap-2 rounded-md px-2 py-2 hover:bg-gray-50 cursor-pointer"
                        >
                          <Checkbox
                            checked={checked}
                            onCheckedChange={(v) => {
                              if (v === "indeterminate") return;
                              if (v !== checked) handleToggleCommunity(comm.value);
                            }}
                          />
                          <span className="text-sm">{comm.label}</span>
                        </label>
                      );
                    })}
                  </div>

                  {formData.communities.length > 0 && (
                    <p className="text-xs text-gray-500">Selected: {formData.communities.join(", ")}</p>
                  )}
                </div>

                {/* Sub-Community Multi-Select */}
                <div className="space-y-2">
                  <Label>Sub-Communities</Label>

                  <div
                    className={`grid grid-cols-1 md:grid-cols-2 gap-2 rounded-lg border p-3 ${!formData.communities.length ? "opacity-50 pointer-events-none" : ""
                      }`}
                  >
                    {availableSubCommunities.length ? (
                      availableSubCommunities.map((sub) => {
                        const checked = formData.subCommunities.includes(sub.value);
                        return (
                          <label
                            key={sub.value}
                            className="flex items-center gap-2 rounded-md px-2 py-2 hover:bg-gray-50 cursor-pointer"
                          >
                            <Checkbox
                              checked={checked}
                              onCheckedChange={(v) => {
                                if (v === "indeterminate") return;
                                if (v !== checked) handleToggleSubCommunity(sub.value);
                              }}
                            />
                            <span className="text-sm">{sub.label}</span>
                          </label>
                        );
                      })
                    ) : (
                      <p className="text-sm text-gray-500">Select at least one community to see sub-communities.</p>
                    )}
                  </div>
                </div>

                {/* Industry (single-select) */}
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div className="space-y-2">
                    <Label htmlFor="industry">Industry *</Label>
                    <Select
                      value={formData.industry}
                      onValueChange={(value) => handleInputChange("industry", value)}
                      disabled={!formData.communities.length}
                    >
                      <SelectTrigger className="bg-white shadow-sm hover:border-gray-400 transition-colors">
                        <SelectValue placeholder={formData.communities.length ? "Select industry" : "Select community first"} />
                      </SelectTrigger>
                      <SelectContent className="bg-white shadow-lg border border-gray-200 rounded-lg max-h-[300px] overflow-y-auto">
                        {filteredIndustries.map((ind) => (
                          <SelectItem
                            key={ind.value}
                            value={ind.value}
                            className="cursor-pointer hover:bg-gray-50 focus:bg-gray-100 py-2.5 px-3"
                          >
                            {ind.label}
                          </SelectItem>
                        ))}
                      </SelectContent>
                    </Select>
                  </div>

                  {/* ✅ Sub-Industry (never blank now) */}
                  <div className="space-y-2">
                    <Label htmlFor="subIndustry">Sub-Industry</Label>
                    <Select
                      value={formData.subIndustry}
                      onValueChange={(value) => handleInputChange("subIndustry", value)}
                      disabled={!formData.industry}
                    >
                      <SelectTrigger className="bg-white shadow-sm hover:border-gray-400 transition-colors">
                        <SelectValue placeholder={!formData.industry ? "Select industry first" : "Select sub-industry"} />
                      </SelectTrigger>
                      <SelectContent className="bg-white shadow-lg border border-gray-200 rounded-lg max-h-[300px] overflow-y-auto">
                        {subIndustryOptions.map((sub) => (
                          <SelectItem
                            key={sub.value}
                            value={sub.value}
                            className="cursor-pointer hover:bg-gray-50 focus:bg-gray-100 py-2.5 px-3"
                          >
                            {sub.label}
                          </SelectItem>
                        ))}
                      </SelectContent>
                    </Select>
                    {!safeSubIndustryMap[formData.industry]?.length && formData.industry ? (
                      <p className="text-xs text-gray-500">
                        Showing common sub-industry options (you can later paste full mapping for {formData.industry})
                      </p>
                    ) : null}
                  </div>
                </div>

              </div>

              {otpState !== "verified" && formData.mobile && (
                <div className="bg-amber-50 border border-amber-200 rounded-lg p-4">
                  <div className="flex items-start gap-3">
                    <AlertCircle className="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" />
                    <div>
                      <p className="text-sm font-medium text-amber-900">Mobile Verification Required</p>
                      <p className="text-sm text-amber-800 mt-1">
                        Please verify your mobile number to complete registration.
                      </p>
                    </div>
                  </div>
                </div>
              )}

              <Button
                type="submit"
                className="w-full bg-emerald-600 hover:bg-emerald-700"
                disabled={isLoading || !isFormValid}
              >
                {isLoading ? (
                  <>
                    <Loader2 className="w-4 h-4 mr-2 animate-spin" />
                    Creating your profile...
                  </>
                ) : (
                  <>
                    <CheckCircle className="w-4 h-4 mr-2" />
                    Complete Registration
                  </>
                )}
              </Button>

              {otpError && (
                <div className="flex items-center gap-2 text-red-600 text-sm">
                  <AlertCircle className="w-4 h-4" />
                  <span>{otpError}</span>
                </div>
              )}
            </form>
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
