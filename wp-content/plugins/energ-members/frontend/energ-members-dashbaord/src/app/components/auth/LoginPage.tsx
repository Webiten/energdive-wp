import { useState, useEffect } from "react";
import { Card, CardContent, CardHeader, CardTitle } from "../ui/card";
import { Button } from "../ui/button";
import { Input } from "../ui/input";
import { Label } from "../ui/label";
import { Mail, ArrowRight, Loader2 } from "lucide-react";
import { AuthAPI } from "@/app/lib/api";

interface LoginPageProps {
  onVerificationSent: (identifier: string) => void;
}

export function LoginPage({ onVerificationSent }: LoginPageProps) {
  const [identifier, setIdentifier] = useState("");
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  // ✅ NEW: Auto-redirect if already logged in (WordPress + JWT)
  useEffect(() => {
    const wpLoggedIn = document.body.classList.contains("logged-in");
    const hasToken =
      !!localStorage.getItem("access_token") ||
      !!localStorage.getItem("auth_token");

    if (wpLoggedIn || hasToken) {
      window.location.href = "/thank-you";
    }
  }, []);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsLoading(true);
    setError(null);

    try {
      await AuthAPI.requestOtp(identifier);
      onVerificationSent(identifier);
    } catch (err: any) {
      setError(err?.message || "Failed to send OTP");
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className="min-h-screen flex items-center justify-center bg-white">
      <div className="w-full max-w-md">

        {/* Logo */}
        <div className="text-center mb-8">
          <div className="inline-flex items-center gap-3 mb-4">
            <div className="w-12 h-12 bg-emerald-600 rounded-lg flex items-center justify-center">
              <span className="text-white font-bold text-xl">E</span>
            </div>
            {/* <div className="text-left">
              <h1 className="text-2xl font-bold text-gray-900">ENERGCLUB</h1>
              <p className="text-sm text-gray-600">
                Energy Intelligence Platform
              </p>
            </div> */}
          </div>
        </div>

        <Card className="shadow-xl">
          <CardHeader>
            <CardTitle className="text-2xl text-center">Welcome</CardTitle>
            <p className="text-center text-gray-600 text-sm mt-2">
              Enter your email or phone to continue
            </p>
          </CardHeader>

          <CardContent>
            <form onSubmit={handleSubmit} className="space-y-6">
              <div className="space-y-2">
                <Label htmlFor="identifier">Email or Phone</Label>

                <div className="relative">
                  <Mail className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" />

                  <Input
                    id="identifier"
                    type="text"
                    placeholder="email or mobile number"
                    value={identifier}
                    onChange={(e) => setIdentifier(e.target.value)}
                    className="pl-10"
                    required
                  />
                </div>

                {error && (
                  <p className="text-sm text-red-600">{error}</p>
                )}

                <p className="text-xs text-gray-500">
                  We'll send you a one-time verification code
                </p>
              </div>

              <Button
                type="submit"
                className="w-full bg-emerald-600 hover:bg-emerald-700"
                disabled={isLoading || !identifier}
              >
                {isLoading ? (
                  <>
                    <Loader2 className="w-4 h-4 mr-2 animate-spin" />
                    Sending OTP...
                  </>
                ) : (
                  <>
                    Continue
                    <ArrowRight className="w-4 h-4 ml-2" />
                  </>
                )}
              </Button>
            </form>

            <div className="mt-6 text-center">
              <p className="text-sm text-gray-600">
                By continuing, you agree to our{" "}
                <a className="text-emerald-600 hover:underline">Terms</a>{" "}
                &{" "}
                <a className="text-emerald-600 hover:underline">
                  Privacy Policy
                </a>
              </p>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
