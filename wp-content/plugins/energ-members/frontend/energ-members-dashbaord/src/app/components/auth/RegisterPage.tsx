import { useState } from "react";
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
import { CheckCircle, Loader2, Phone, AlertCircle } from "lucide-react";
import { AuthAPI } from "@/app/lib/api";

interface RegisterPageProps {
  email: string;
  onRegistrationComplete: () => void;
}

/** 🔋 FINAL COMMUNITIES */
const communities = [
  "Oil & Gas",
  "Power Generation",
  "Renewables",
  "Transmission",
  "Distribution",
  "Electricity Markets",
  "New Energies",
  "Energy Storage Systems",
  "Sustainability",
];

const subCommunities: Record<string, string[]> = {
  "Oil & Gas": ["Upstream", "Pipelines", "Refining", "Petrochemicals", "CGD", "LPG", "Retail", "Oil Markets"],
  "Power Generation": ["Thermal", "Nuclear"],
  "Renewables": ["Solar", "Wind", "Hydro", "Biopower", "Cogeneration", "Waste-to-Energy"],
  "Transmission": ["Smart Grid"],
  "Distribution": ["Smart Meters & AMI", "EV Charging", "Data Centres", "Smart Cities", "Railways & Metros"],
  "Electricity Markets": ["Power Markets", "Carbon Markets", "RCO"],
  "New Energies": ["Green Hydrogen", "E-Fuels"],
  "Energy Storage Systems": ["BESS", "Pumped Hydro", "CAES", "Thermal", "Flywheel"],
  "Sustainability": ["Energy Efficiency", "Occupational Health", "Industrial & Process Safety", "Environment"],
};

export function RegisterPage({ email, onRegistrationComplete }: RegisterPageProps) {
  const [loading, setLoading] = useState(false);
  const [phone, setPhone] = useState("");
  const [otp, setOtp] = useState("");
  const [otpState, setOtpState] = useState<"idle" | "sent" | "verifying" | "verified">("idle");
  const [error, setError] = useState("");

  const [form, setForm] = useState({
    first_name: "",
    last_name: "",
    country: "",
    state: "",
    community: "",
    sub_community: "",
  });

  /** 📱 SEND PHONE OTP */
  const sendPhoneOtp = async () => {
    try {
      setError("");
      setOtpState("sent");
      await AuthAPI.requestOtp(phone);
    } catch (e: any) {
      setError(e.message || "Failed to send OTP");
      setOtpState("idle");
    }
  };

  /** 🔐 VERIFY PHONE OTP */
  const verifyPhoneOtp = async () => {
    try {
      setOtpState("verifying");
      await AuthAPI.verifyOtp(phone, otp);
      setOtpState("verified");
    } catch (e: any) {
      setError(e.message || "Invalid OTP");
      setOtpState("sent");
    }
  };

  /** ✅ SUBMIT REGISTRATION */
  const submitRegistration = async (e: React.FormEvent) => {
    e.preventDefault();

    if (otpState !== "verified") {
      setError("Please verify your phone number");
      return;
    }

    try {
      setLoading(true);
      await AuthAPI.completeRegistration({
        ...form,
        phone,
      });
      onRegistrationComplete();
    } catch (e: any) {
      setError(e.message || "Registration failed");
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen flex items-center justify-center bg-gray-50 px-6">
      <Card className="w-full max-w-2xl">
        <CardHeader>
          <CardTitle className="text-center text-2xl">
            Complete Registration
          </CardTitle>
        </CardHeader>

        <CardContent>
          <form onSubmit={submitRegistration} className="space-y-5">

            <Input
              placeholder="First Name"
              value={form.first_name}
              onChange={(e) => setForm({ ...form, first_name: e.target.value })}
              required
            />

            <Input
              placeholder="Last Name"
              value={form.last_name}
              onChange={(e) => setForm({ ...form, last_name: e.target.value })}
              required
            />

            <Input value={email} disabled />

            {/* PHONE */}
            <div className="space-y-2">
              <Label>Phone Number</Label>
              <div className="flex gap-2">
                <Input
                  value={phone}
                  onChange={(e) => setPhone(e.target.value.replace(/\D/g, ""))}
                  disabled={otpState === "verified"}
                />

                {otpState === "idle" && (
                  <Button type="button" onClick={sendPhoneOtp}>
                    <Phone className="w-4 h-4 mr-2" /> Send OTP
                  </Button>
                )}

                {otpState === "verified" && (
                  <CheckCircle className="text-green-600 mt-2" />
                )}
              </div>
            </div>

            {otpState === "sent" && (
              <div className="flex gap-2">
                <Input
                  placeholder="Enter OTP"
                  value={otp}
                  onChange={(e) => setOtp(e.target.value)}
                />
                <Button type="button" onClick={verifyPhoneOtp}>
                  Verify
                </Button>
              </div>
            )}

            {/* COMMUNITY */}
            <Select
              value={form.community}
              onValueChange={(v) => setForm({ ...form, community: v, sub_community: "" })}
            >
              <SelectTrigger>
                <SelectValue placeholder="Select Community" />
              </SelectTrigger>
              <SelectContent>
                {communities.map((c) => (
                  <SelectItem key={c} value={c}>{c}</SelectItem>
                ))}
              </SelectContent>
            </Select>

            {form.community && (
              <Select
                value={form.sub_community}
                onValueChange={(v) => setForm({ ...form, sub_community: v })}
              >
                <SelectTrigger>
                  <SelectValue placeholder="Select Sub-Community" />
                </SelectTrigger>
                <SelectContent>
                  {subCommunities[form.community]?.map((s) => (
                    <SelectItem key={s} value={s}>{s}</SelectItem>
                  ))}
                </SelectContent>
              </Select>
            )}

            {error && (
              <div className="text-sm text-red-600 flex gap-2">
                <AlertCircle className="w-4 h-4" /> {error}
              </div>
            )}

            <Button
              type="submit"
              disabled={loading}
              className="w-full bg-emerald-600"
            >
              {loading ? (
                <>
                  <Loader2 className="w-4 h-4 mr-2 animate-spin" />
                  Creating Account...
                </>
              ) : (
                "Complete Registration"
              )}
            </Button>

          </form>
        </CardContent>
      </Card>
    </div>
  );
}
