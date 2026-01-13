import { useState, useEffect } from "react";
import { Card, CardContent, CardHeader, CardTitle } from "../ui/card";
import { Button } from "../ui/button";
import { Input } from "../ui/input";
import {
  Mail,
  CheckCircle,
  XCircle,
  Loader2,
  RefreshCw,
} from "lucide-react";
import { AuthAPI } from "@/app/lib/api";

interface VerificationPageProps {
  identifier: string;
  onVerified: (isNewUser: boolean) => void;
  onResend: () => Promise<void>;
}

type VerificationState =
  | "sent"
  | "verifying"
  | "verified"
  | "expired"
  | "invalid";

export function VerificationPage({
  identifier,
  onVerified,
  onResend,
}: VerificationPageProps) {
  const [state, setState] = useState<VerificationState>("sent");
  const [otp, setOtp] = useState("");
  const [countdown, setCountdown] = useState(60);
  const [canResend, setCanResend] = useState(false);
  const [error, setError] = useState<string | null>(null);

  /** ⏱ Countdown */
  useEffect(() => {
    if (state !== "sent") return;

    if (countdown > 0) {
      const timer = setTimeout(() => setCountdown((c) => c - 1), 1000);
      return () => clearTimeout(timer);
    }

    setCanResend(true);
  }, [countdown, state]);

  /** 🔐 VERIFY OTP */
  const handleVerify = async () => {
    if (!identifier || otp.length !== 6) {
      setError("Identifier missing. Please request OTP again.");
      return;
    }
    
    setState("verifying");
    setError(null);

    try {
      const res = await AuthAPI.verifyOtp(identifier, otp);

      localStorage.setItem("access_token", res.access_token);
      localStorage.setItem("refresh_token", res.refresh_token);

      setOtp("");
      setState("verified");

      setTimeout(() => {
        onVerified(res.is_new_user);
      }, 1000);
    } catch (err: any) {
      const code = err?.code || err?.data?.code;
      const message = err?.message || err?.data?.message || "Invalid OTP";

      if (code === "otp_expired") setState("expired");
      else setState("invalid");

      setError(message);
    }
  };

  /** 🔁 RESEND OTP */
  const handleResend = async () => {
    try {
      setCountdown(60);
      setCanResend(false);
      setState("sent");
      setOtp("");
      setError(null);

      await onResend();
    } catch {
      setError("Failed to resend OTP. Try again.");
    }
  };

  return (
    <div className="min-h-screen bg-gradient-to-br from-emerald-50 to-gray-50 flex items-center justify-center p-6">
      <div className="w-full max-w-md">
        <Card className="shadow-xl">
          <CardHeader className="text-center">
            <div className="flex justify-center mb-4">
              {state === "sent" && (
                <Mail className="w-10 h-10 text-emerald-600" />
              )}
              {state === "verifying" && (
                <Loader2 className="w-10 h-10 animate-spin text-blue-600" />
              )}
              {state === "verified" && (
                <CheckCircle className="w-10 h-10 text-emerald-600" />
              )}
              {(state === "expired" || state === "invalid") && (
                <XCircle className="w-10 h-10 text-red-600" />
              )}
            </div>

            <CardTitle>
              {state === "sent" && "Enter Verification Code"}
              {state === "verifying" && "Verifying"}
              {state === "verified" && "Verified"}
              {state === "expired" && "OTP Expired"}
              {state === "invalid" && "Invalid OTP"}
            </CardTitle>

            <p className="text-sm text-gray-600 mt-2">
              Sent to <strong>{identifier}</strong>
            </p>
          </CardHeader>

          <CardContent className="space-y-4">
            {state === "sent" && (
              <>
                <Input
                  value={otp}
                  onChange={(e) =>
                    setOtp(e.target.value.replace(/\D/g, ""))
                  }
                  placeholder="Enter 6-digit OTP"
                  maxLength={6}
                />

                {error && (
                  <p className="text-sm text-red-600">{error}</p>
                )}

                <Button
                  className="w-full bg-emerald-600 hover:bg-emerald-700"
                  onClick={handleVerify}
                  disabled={otp.length !== 6}
                >
                  Verify OTP
                </Button>

                <Button
                  variant="outline"
                  onClick={handleResend}
                  disabled={!canResend}
                  className="w-full"
                >
                  <RefreshCw className="w-4 h-4 mr-2" />
                  {canResend ? "Resend OTP" : `Resend in ${countdown}s`}
                </Button>
              </>
            )}

            {state === "verifying" && (
              <p className="text-center text-sm text-gray-600">
                Please wait...
              </p>
            )}

            {state === "verified" && (
              <p className="text-center text-sm text-gray-600">
                Redirecting…
              </p>
            )}

            {(state === "expired" || state === "invalid") && (
              <>
                <p className="text-sm text-center text-gray-600">
                  {error}
                </p>
                <Button
                  onClick={handleResend}
                  className="w-full bg-emerald-600"
                >
                  Request New OTP
                </Button>
              </>
            )}
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
