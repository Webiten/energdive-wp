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

// Mock data for dropdowns
const communities = [
  { value: "energy-producers", label: "Energy Producers" },
  { value: "utilities", label: "Utilities & Grid Operators" },
  { value: "industrial", label: "Industrial & Commercial" },
  { value: "consultancy", label: "Consultancy & Advisory" },
  { value: "policy", label: "Policy & Government" },
  { value: "research", label: "Research & Academia" },
  { value: "finance", label: "Finance & Investment" },
];

const subCommunityMap: Record<string, Array<{ value: string; label: string }>> = {
  "energy-producers": [
    { value: "renewable", label: "Renewable Energy Developers" },
    { value: "oil-gas", label: "Oil & Gas Operators" },
    { value: "nuclear", label: "Nuclear Power Operators" },
  ],
  "utilities": [
    { value: "distribution", label: "Distribution Companies" },
    { value: "transmission", label: "Transmission Operators" },
    { value: "retail", label: "Retail Energy Providers" },
  ],
  "consultancy": [
    { value: "strategy", label: "Strategy Consulting" },
    { value: "engineering", label: "Engineering Services" },
    { value: "sustainability", label: "Sustainability Advisors" },
  ],
};

const industries = [
  { value: "power-generation", label: "Power Generation" },
  { value: "oil-gas", label: "Oil & Gas" },
  { value: "renewable", label: "Renewable Energy" },
  { value: "utilities", label: "Utilities" },
  { value: "energy-services", label: "Energy Services" },
];

const subIndustryMap: Record<string, Array<{ value: string; label: string }>> = {
  "power-generation": [
    { value: "thermal", label: "Thermal Power" },
    { value: "nuclear", label: "Nuclear Power" },
    { value: "hydro", label: "Hydroelectric" },
  ],
  "renewable": [
    { value: "solar", label: "Solar Energy" },
    { value: "wind", label: "Wind Energy" },
    { value: "hydrogen", label: "Hydrogen" },
  ],
};

const areaOfIndustryMap: Record<string, Array<{ value: string; label: string }>> = {
  "renewable": [
    { value: "project-development", label: "Project Development" },
    { value: "operations", label: "Operations & Maintenance" },
    { value: "policy-regulation", label: "Policy & Regulation" },
    { value: "market-analysis", label: "Market Analysis" },
    { value: "technology", label: "Technology & Innovation" },
  ],
};

const countries = [
  { value: "usa", label: "United States" },
  { value: "uk", label: "United Kingdom" },
  { value: "india", label: "India" },
  { value: "germany", label: "Germany" },
  { value: "france", label: "France" },
];

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
  const [otpState, setOtpState] = useState<'idle' | 'sent' | 'verifying' | 'verified' | 'error'>('idle');
  const [otp, setOtp] = useState(['', '', '', '', '', '']);
  const [otpError, setOtpError] = useState('');
  const [resendTimer, setResendTimer] = useState(0);
  const [mobileChanged, setMobileChanged] = useState(false);

  // Timer effect for resend countdown
  useEffect(() => {
    if (resendTimer > 0) {
      const timer = setTimeout(() => setResendTimer(resendTimer - 1), 1000);
      return () => clearTimeout(timer);
    }
  }, [resendTimer]);

  const handleMobileChange = (value: string) => {
    handleInputChange("mobile", value);
    if (otpState === 'verified') {
      setOtpState('idle');
      setOtp(['', '', '', '', '', '']);
      setMobileChanged(true);
    }
  };

  const handleSendOTP = () => {
    if (!formData.mobile || formData.mobile.length < 10) {
      setOtpError('Please enter a valid mobile number');
      return;
    }
    
    setOtpState('sent');
    setResendTimer(60);
    setOtpError('');
    setMobileChanged(false);
    
    // Simulate sending OTP
    console.log(`Sending OTP to ${formData.countryCode} ${formData.mobile}`);
  };

  const handleResendOTP = () => {
    if (resendTimer > 0) return;
    handleSendOTP();
  };

  const handleOTPChange = (index: number, value: string) => {
    if (value.length > 1) return; // Only allow single digit
    
    const newOtp = [...otp];
    newOtp[index] = value;
    setOtp(newOtp);
    setOtpError('');
    
    // Auto-focus next input
    if (value && index < 5) {
      const nextInput = document.getElementById(`otp-${index + 1}`);
      nextInput?.focus();
    }
    
    // Auto-verify when all 6 digits are entered
    if (newOtp.every(digit => digit !== '') && index === 5) {
      verifyOTP(newOtp.join(''));
    }
  };

  const handleOTPKeyDown = (index: number, e: React.KeyboardEvent<HTMLInputElement>) => {
    if (e.key === 'Backspace' && !otp[index] && index > 0) {
      const prevInput = document.getElementById(`otp-${index - 1}`);
      prevInput?.focus();
    }
  };

  const verifyOTP = (otpValue: string) => {
    setOtpState('verifying');
    
    // Simulate OTP verification (in real app, this would be an API call)
    setTimeout(() => {
      // For demo purposes, accept any 6-digit code
      if (otpValue.length === 6) {
        setOtpState('verified');
        setOtpError('');
      } else {
        setOtpState('error');
        setOtpError('Invalid OTP. Please try again.');
        setTimeout(() => setOtpState('sent'), 2000);
      }
    }, 1500);
  };

  const handleInputChange = (field: string, value: string) => {
    setFormData(prev => {
      const updated = { ...prev, [field]: value };
      
      // Reset dependent fields
      if (field === "community") {
        updated.subCommunity = "";
        updated.areaOfIndustry = "";
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

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    
    // Check if OTP is verified
    if (otpState !== 'verified') {
      setOtpError('Please verify your mobile number before submitting');
      return;
    }
    
    setIsLoading(true);
    
    // Simulate API call
    setTimeout(() => {
      setIsLoading(false);
      onRegistrationComplete();
    }, 2000);
  };

  const isFormValid = formData.firstName && formData.lastName && formData.mobile && 
                       formData.country && formData.community && formData.industry && otpState === 'verified';

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
                  <Input
                    id="email"
                    type="email"
                    value={email}
                    disabled
                    className="bg-gray-50"
                  />
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
                      disabled={otpState === 'verified'}
                    >
                      <SelectTrigger className="w-[140px]">
                        <SelectValue />
                      </SelectTrigger>
                      <SelectContent>
                        {countryCodes.map(code => (
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
                        disabled={otpState === 'verified'}
                        className={otpState === 'verified' ? 'bg-gray-50' : ''}
                        required
                      />
                      
                      {/* Send/Resend OTP Button */}
                      {otpState === 'idle' || otpState === 'error' ? (
                        <Button
                          type="button"
                          onClick={handleSendOTP}
                          className="bg-emerald-600 hover:bg-emerald-700 whitespace-nowrap"
                        >
                          <Phone className="w-4 h-4 mr-2" />
                          Send OTP
                        </Button>
                      ) : otpState === 'verified' ? (
                        <div className="flex items-center gap-2 px-4 py-2 bg-green-50 border border-green-200 rounded-md">
                          <CheckCircle className="w-4 h-4 text-green-600" />
                          <span className="text-sm text-green-700 font-medium">Verified</span>
                        </div>
                      ) : null}
                    </div>
                  </div>

                  <p className="text-xs text-gray-500">
                    {otpState === 'idle' 
                      ? 'We\'ll send you a 6-digit code to verify your number and ensure community trust'
                      : otpState === 'verified'
                      ? '✓ Mobile number verified successfully'
                      : ''}
                  </p>

                  {/* OTP Input Section */}
                  {(otpState === 'sent' || otpState === 'verifying' || otpState === 'error') && (
                    <div className="mt-4 p-4 bg-gray-50 border border-gray-200 rounded-lg space-y-4">
                      <div className="flex items-start gap-2">
                        <div className="flex-1">
                          <p className="text-sm font-medium text-gray-900">Enter Verification Code</p>
                          <p className="text-xs text-gray-600 mt-1">
                            We sent a 6-digit code to {formData.countryCode} {formData.mobile}
                          </p>
                        </div>
                        {otpState === 'verifying' && (
                          <Loader2 className="w-5 h-5 text-emerald-600 animate-spin" />
                        )}
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
                            onChange={(e) => handleOTPChange(index, e.target.value.replace(/[^0-9]/g, ''))}
                            onKeyDown={(e) => handleOTPKeyDown(index, e)}
                            className={`w-12 h-12 text-center text-lg font-semibold ${
                              otpState === 'error' ? 'border-red-500' : ''
                            }`}
                            disabled={otpState === 'verifying'}
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
                            onClick={handleResendOTP}
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
                        {countries.map(country => (
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
                        {communities.map(comm => (
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
                        {formData.community && subCommunityMap[formData.community]?.map(sub => (
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
                    <Select value={formData.industry} onValueChange={(value) => handleInputChange("industry", value)}>
                      <SelectTrigger>
                        <SelectValue placeholder="Select industry" />
                      </SelectTrigger>
                      <SelectContent>
                        {industries.map(ind => (
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
                      disabled={!formData.industry}
                    >
                      <SelectTrigger>
                        <SelectValue placeholder={formData.industry ? "Select sub-industry" : "Select industry first"} />
                      </SelectTrigger>
                      <SelectContent>
                        {formData.industry && subIndustryMap[formData.industry]?.map(sub => (
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
                    <SelectContent>
                      {formData.subCommunity && areaOfIndustryMap[formData.subCommunity]?.map(area => (
                        <SelectItem key={area.value} value={area.value}>
                          {area.label}
                        </SelectItem>
                      ))}
                    </SelectContent>
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
              {otpState !== 'verified' && formData.mobile && (
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