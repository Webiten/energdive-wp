import { useState, useEffect } from "react";
import { Card, CardContent, CardHeader, CardTitle } from "../ui/card";
import { Button } from "../ui/button";
import { Input } from "../ui/input";
import { Label } from "../ui/label";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "../ui/select";
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

// ===== REAL Communities/Sub-Communities =====
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

// ===== REAL Industries =====
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

// Sub-Industry mapping (expand when you provide a full breakdown)
const subIndustryMap: Record<string, Array<{ value: string; label: string }>> = {
  "oil-gas": [
    { value: "upstream", label: "Upstream" },
    { value: "midstream", label: "Midstream" },
    { value: "downstream", label: "Downstream" },
    { value: "refining", label: "Refining" },
    { value: "petrochemicals", label: "Petrochemicals" },
    { value: "pipelines", label: "Pipelines" },
    { value: "cgd", label: "CGD" },
  ],
  power: [
    { value: "generation", label: "Generation" },
    { value: "transmission", label: "Transmission" },
    { value: "distribution", label: "Distribution" },
    { value: "trading", label: "Trading" },
  ],
  renewable: [
    { value: "solar", label: "Solar" },
    { value: "wind", label: "Wind" },
    { value: "hydro", label: "Hydro" },
    { value: "biopower", label: "Biopower" },
  ],
  "battery-storage": [
    { value: "bess", label: "BESS" },
    { value: "pumped-hydro", label: "Pumped Hydro" },
    { value: "thermal-storage", label: "Thermal Storage" },
  ],
  "electricity-markets": [
    { value: "power-markets", label: "Power Markets" },
    { value: "carbon-markets", label: "Carbon Markets" },
    { value: "rco", label: "RCO" },
  ],
  transmission: [{ value: "smart-grid", label: "Smart Grid" }],
  "ev-charging": [
    { value: "ac-charging", label: "AC Charging" },
    { value: "dc-fast-charging", label: "DC Fast Charging" },
    { value: "charging-infra", label: "Charging Infrastructure" },
  ],
  environment: [
    { value: "compliance", label: "Compliance" },
    { value: "monitoring", label: "Monitoring" },
    { value: "esg", label: "ESG" },
  ],
};

// Community -> allowed industries mapping (filters Industry based on selected Community)
const communityIndustryMap: Record<string, string[]> = {
  "oil-gas": [
    "oil-gas",
    "chemical",
    "engineering",
    "logistics",
    "mining",
    "infrastructure",
    "government",
    "consulting",
    "distribution",
    "shipping",
    "railways",
    "telecommunication",
    "it",
    "environment",
    "construction-material",
    "exporters-importers",
  ],
  "power-generation": [
    "power",
    "electrical",
    "engineering",
    "construction-material",
    "infrastructure",
    "government",
    "consulting",
    "environment",
    "it",
    "logistics",
    "iron-steel",
    "mining",
    "consumer-durables",
  ],
  renewables: [
    "renewable",
    "battery-storage",
    "electrical",
    "engineering",
    "construction-material",
    "infrastructure",
    "government",
    "consulting",
    "environment",
    "it",
    "logistics",
    "mining",
    "chemical",
    "ev-charging",
  ],
  transmission: [
    "transmission",
    "electrical",
    "engineering",
    "infrastructure",
    "government",
    "consulting",
    "environment",
    "it",
    "telecommunication",
    "construction-material",
    "iron-steel",
    "logistics",
  ],
  distribution: [
    "distribution",
    "electrical",
    "engineering",
    "it",
    "telecommunication",
    "infrastructure",
    "government",
    "consulting",
    "environment",
    "consumer-durables",
    "office-automation",
    "retail",
    "ev-charging",
    "logistics",
  ],
  "electricity-markets": [
    "electricity-markets",
    "bfsi",
    "consulting",
    "government",
    "it",
    "telecommunication",
    "publishing",
    "media",
    "power",
    "renewable",
  ],
  "new-energies": [
    "battery-storage",
    "renewable",
    "chemical",
    "engineering",
    "electrical",
    "oil-gas",
    "power",
    "consulting",
    "government",
    "it",
    "environment",
    "infrastructure",
    "logistics",
  ],
  "energy-storage-systems": [
    "battery-storage",
    "power",
    "renewable",
    "electrical",
    "engineering",
    "chemical",
    "consulting",
    "government",
    "it",
    "environment",
    "infrastructure",
    "logistics",
  ],
  sustainability: [
    "environment",
    "energy-efficiency-management",
    "consulting",
    "government",
    "it",
    "facility-management",
    "engineering",
    "construction-material",
    "power",
    "renewable",
    "chemical",
    "fmcg",
    "healthcare",
    "mining",
    "iron-steel",
    "textile",
  ],
};

// Countries dropdown (keep as-is unless you share full list)
const countries = [
  { value: "usa", label: "United States" },
  { value: "uk", label: "United Kingdom" },
  { value: "india", label: "India" },
  { value: "germany", label: "Germany" },
  { value: "france", label: "France" },
];

function normalizePhone(countryCode: string, mobile: string) {
  const cleaned = (mobile || "").replace(/[^\d]/g, "");
  const cc = (countryCode || "").replace(/[^\d+]/g, "");
  const ccDigits = cc.startsWith("+") ? cc : `+${cc}`;
  return `${ccDigits}${cleaned}`;
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
    community: "",
    subCommunity: "",
    industry: "",
    subIndustry: "",
    areaOfIndustry: "",
  });

  // OTP verification state
  const [otpState, setOtpState] = useState<"idle" | "sent" | "verifying" | "verified" | "error">("idle");
  const [otp, setOtp] = useState(["", "", "", "", "", ""]);
  const [otpError, setOtpError] = useState("");
  const [resendTimer, setResendTimer] = useState(0);
  const [mobileChanged, setMobileChanged] = useState(false);

  // Filter industries based on selected Community
  const filteredIndustries = (() => {
    const allowed = communityIndustryMap[formData.community];
    if (!formData.community || !allowed?.length) return industries;
    return industries.filter((i) => allowed.includes(i.value));
  })();

  // Timer effect for resend countdown
  useEffect(() => {
    if (resendTimer > 0) {
      const timer = setTimeout(() => setResendTimer(resendTimer - 1), 1000);
      return () => clearTimeout(timer);
    }
  }, [resendTimer]);

  const handleMobileChange = (value: string) => {
    handleInputChange("mobile", value);
    if (otpState === "verified") {
      setOtpState("idle");
      setOtp(["", "", "", "", "", ""]);
      setMobileChanged(true);
    }
  };

  const handleSendOTP = async () => {
    if (!formData.mobile || formData.mobile.replace(/[^\d]/g, "").length < 8) {
      setOtpError("Please enter a valid mobile number");
      return;
    }

    setOtpState("sent");
    setResendTimer(60);
    setOtpError("");
    setMobileChanged(false);

    try {
      const phoneIdentifier = normalizePhone(formData.countryCode, formData.mobile);
      await AuthAPI.requestOtp(phoneIdentifier, "register_phone");
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

    if (value && index < 5) {
      const nextInput = document.getElementById(`otp-${index + 1}`);
      nextInput?.focus();
    }

    if (newOtp.every((digit) => digit !== "") && index === 5) {
      void verifyOTP(newOtp.join(""));
    }
  };

  const handleOTPKeyDown = (index: number, e: React.KeyboardEvent<HTMLInputElement>) => {
    if (e.key === "Backspace" && !otp[index] && index > 0) {
      const prevInput = document.getElementById(`otp-${index - 1}`);
      prevInput?.focus();
    }
  };

  const verifyOTP = async (otpValue: string) => {
    setOtpState("verifying");

    try {
      const phoneIdentifier = normalizePhone(formData.countryCode, formData.mobile);
      const res = await AuthAPI.verifyOtp(phoneIdentifier, otpValue);

      // Do not override existing (email) session tokens here
      if (res?.success === false) {
        throw new Error(res?.message || "Invalid OTP. Please try again.");
      }

      setOtpState("verified");
      setOtpError("");
    } catch (err: any) {
      const code = err?.code || err?.data?.code;
      const message = err?.message || err?.data?.message || "Invalid OTP. Please try again.";

      setOtpState("error");
      setOtpError(message);

      if (code === "otp_expired") {
        setTimeout(() => setOtpState("sent"), 500);
      } else {
        setTimeout(() => setOtpState("sent"), 2000);
      }
    }
  };

  const handleInputChange = (field: string, value: string) => {
    setFormData((prev) => {
      const updated = { ...prev, [field]: value };

      // Reset dependent fields
      if (field === "community") {
        updated.subCommunity = "";
        updated.areaOfIndustry = "";
        updated.industry = "";
        updated.subIndustry = "";
      }
      if (field === "industry") {
        updated.subIndustry = "";
      }
      if (field === "subCommunity") {
        updated.areaOfIndustry = "";
      }

      return updated;
    });
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
      const phoneIdentifier = normalizePhone(formData.countryCode, formData.mobile);

      await AuthAPI.completeRegistration({
        first_name: formData.firstName,
        last_name: formData.lastName,
        email: email,
        phone: phoneIdentifier,
        country: formData.country,
        state: formData.state,
        community: formData.community,
        sub_community: formData.subCommunity,
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
    formData.community &&
    formData.industry &&
    otpState === "verified";

  return (
    <div className="min-h-screen bg-gradient-to-br from-emerald-50 to-gray-50 py-12 px-6">
      <div className="max-w-3xl mx-auto">
        {/* Logo */}
        <div className="text-center mb-8">
          <div className="inline-flex items-center gap-3 mb-4">
            <div className="w-12 h-12 bg-emerald-600 rounded-lg flex items-center justify-center">
              <span className="text-white font-bold text-xl">E</span>
            </div>
            <div className="text-left">
              <h1 className="text-2xl font-bold text-gray-900">ENERGCLUB</h1>
              <p className="text-sm text-gray-600">Energy Intelligence Platform</p>
            </div>
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
                <h3 className="text-lg font-semibold text-gray-900 border-b pb-2">
                  Personal Information
                </h3>

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

                {/* Mobile Number with OTP Verification */}
                <div className="space-y-3">
                  <Label htmlFor="mobile">Mobile Number *</Label>

                  <div className="flex gap-2">
                    {/* Country Code Selector */}
                    <Select
                      value={formData.countryCode}
                      onValueChange={(value) => handleInputChange("countryCode", value)}
                      disabled={otpState === "verified"}
                    >
                      <SelectTrigger className="w-[140px]">
                        <SelectValue />
                      </SelectTrigger>
                      <SelectContent>
                        {countryCodes.map((code) => (
                          <SelectItem key={code.value} value={code.value}>
                            <span className="flex items-center gap-2">
                              <span>{code.flag}</span>
                              <span>{code.value}</span>
                            </span>
                          </SelectItem>
                        ))}
                      </SelectContent>
                    </Select>

                    {/* Mobile Number Input */}
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

                      {/* Send/Resend OTP Button */}
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

                  <p className="text-xs text-gray-500">
                    {otpState === "idle"
                      ? "We'll send you a 6-digit code to verify your number and ensure community trust"
                      : otpState === "verified"
                      ? "✓ Mobile number verified successfully"
                      : ""}
                  </p>

                  {/* OTP Input Section */}
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

                      {/* OTP Input Fields */}
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
                            className={`w-12 h-12 text-center text-lg font-semibold ${
                              otpState === "error" ? "border-red-500" : ""
                            }`}
                            disabled={otpState === "verifying"}
                          />
                        ))}
                      </div>

                      {/* Error Message */}
                      {otpError && (
                        <div className="flex items-center gap-2 text-red-600 text-sm">
                          <AlertCircle className="w-4 h-4" />
                          <span>{otpError}</span>
                        </div>
                      )}

                      {/* Resend Timer/Button */}
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
                        {countries.map((country) => (
                          <SelectItem key={country.value} value={country.value}>
                            {country.label}
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

              {/* Professional Classification */}
              <div className="space-y-4">
                <h3 className="text-lg font-semibold text-gray-900 border-b pb-2">
                  Professional Classification
                </h3>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div className="space-y-2">
                    <Label htmlFor="community">Community *</Label>
                    <Select value={formData.community} onValueChange={(value) => handleInputChange("community", value)}>
                      <SelectTrigger>
                        <SelectValue placeholder="Select community" />
                      </SelectTrigger>
                      <SelectContent>
                        {communities.map((comm) => (
                          <SelectItem key={comm.value} value={comm.value}>
                            {comm.label}
                          </SelectItem>
                        ))}
                      </SelectContent>
                    </Select>
                  </div>

                  <div className="space-y-2">
                    <Label htmlFor="subCommunity">Sub-Community</Label>
                    <Select
                      value={formData.subCommunity}
                      onValueChange={(value) => handleInputChange("subCommunity", value)}
                      disabled={!formData.community}
                    >
                      <SelectTrigger>
                        <SelectValue placeholder={formData.community ? "Select sub-community" : "Select community first"} />
                      </SelectTrigger>
                      <SelectContent>
                        {formData.community &&
                          subCommunityMap[formData.community]?.map((sub) => (
                            <SelectItem key={sub.value} value={sub.value}>
                              {sub.label}
                            </SelectItem>
                          ))}
                      </SelectContent>
                    </Select>
                  </div>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div className="space-y-2">
                    <Label htmlFor="industry">Industry *</Label>
                    <Select
                      value={formData.industry}
                      onValueChange={(value) => handleInputChange("industry", value)}
                      disabled={!formData.community}
                    >
                      <SelectTrigger>
                        <SelectValue placeholder={formData.community ? "Select industry" : "Select community first"} />
                      </SelectTrigger>
                      <SelectContent>
                        {filteredIndustries.map((ind) => (
                          <SelectItem key={ind.value} value={ind.value}>
                            {ind.label}
                          </SelectItem>
                        ))}
                      </SelectContent>
                    </Select>
                  </div>

                  <div className="space-y-2">
                    <Label htmlFor="subIndustry">Sub-Industry</Label>
                    <Select
                      value={formData.subIndustry}
                      onValueChange={(value) => handleInputChange("subIndustry", value)}
                      disabled={!formData.industry || !(subIndustryMap[formData.industry]?.length)}
                    >
                      <SelectTrigger>
                        <SelectValue
                          placeholder={
                            !formData.industry
                              ? "Select industry first"
                              : subIndustryMap[formData.industry]?.length
                              ? "Select sub-industry"
                              : "No sub-industries available"
                          }
                        />
                      </SelectTrigger>
                      <SelectContent>
                        {formData.industry &&
                          subIndustryMap[formData.industry]?.map((sub) => (
                            <SelectItem key={sub.value} value={sub.value}>
                              {sub.label}
                            </SelectItem>
                          ))}
                      </SelectContent>
                    </Select>
                  </div>
                </div>

                <div className="space-y-2">
                  <Label htmlFor="areaOfIndustry">Area of Industry</Label>
                  <Select
                    value={formData.areaOfIndustry}
                    onValueChange={(value) => handleInputChange("areaOfIndustry", value)}
                    disabled={!formData.subCommunity}
                  >
                    <SelectTrigger>
                      <SelectValue placeholder={formData.subCommunity ? "Select area" : "Select sub-community first"} />
                    </SelectTrigger>
                    <SelectContent>{/* Keep UI intact; mapping can be added later */}</SelectContent>
                  </Select>
                  <p className="text-xs text-gray-500">
                    This will personalize your dashboard intelligence modules
                  </p>
                </div>
              </div>

              <div className="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p className="text-sm text-blue-900">
                  <strong>Note:</strong> Your selections will be used to personalize your dashboard with relevant intelligence, reports, and analytics tailored to your professional interests.
                </p>
              </div>

              {/* Registration Requirement Notice */}
              {otpState !== "verified" && formData.mobile && (
                <div className="bg-amber-50 border border-amber-200 rounded-lg p-4">
                  <div className="flex items-start gap-3">
                    <AlertCircle className="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" />
                    <div>
                      <p className="text-sm font-medium text-amber-900">Mobile Verification Required</p>
                      <p className="text-sm text-amber-800 mt-1">
                        Please verify your mobile number to complete registration. This helps us maintain a trusted professional community.
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
            </form>
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
