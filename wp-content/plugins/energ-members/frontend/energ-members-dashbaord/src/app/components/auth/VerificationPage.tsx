import { useState, useEffect } from "react";
import { Card, CardContent, CardHeader, CardTitle } from "../ui/card";
import { Button } from "../ui/button";
import { Mail, CheckCircle, XCircle, Loader2, RefreshCw } from "lucide-react";

interface VerificationPageProps {
  email: string;
  onVerified: (isNewUser: boolean) => void;
  onResend: () => void;
}

type VerificationState = 'sent' | 'verifying' | 'verified' | 'expired' | 'invalid';

export function VerificationPage({ email, onVerified, onResend }: VerificationPageProps) {
  const [state, setState] = useState<VerificationState>('sent');
  const [countdown, setCountdown] = useState(60);
  const [canResend, setCanResend] = useState(false);

  useEffect(() => {
    if (countdown > 0 && state === 'sent') {
      const timer = setTimeout(() => setCountdown(countdown - 1), 1000);
      return () => clearTimeout(timer);
    } else if (countdown === 0) {
      setCanResend(true);
    }
  }, [countdown, state]);

  const handleResend = () => {
    setCountdown(60);
    setCanResend(false);
    setState('sent');
    onResend();
  };

  const simulateVerification = () => {
    setState('verifying');
    // Simulate checking verification
    setTimeout(() => {
      setState('verified');
      // Simulate checking if user is new or existing (50/50 for demo)
      const isNewUser = Math.random() > 0.5;
      setTimeout(() => {
        onVerified(isNewUser);
      }, 1500);
    }, 2000);
  };

  return (
    <div className="min-h-screen bg-gradient-to-br from-emerald-50 to-gray-50 flex items-center justify-center p-6">
      <div className="w-full max-w-md">
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
            <div className="flex flex-col items-center">
              {state === 'sent' && (
                <div className="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mb-4">
                  <Mail className="w-8 h-8 text-emerald-600" />
                </div>
              )}
              {state === 'verifying' && (
                <div className="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                  <Loader2 className="w-8 h-8 text-blue-600 animate-spin" />
                </div>
              )}
              {state === 'verified' && (
                <div className="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mb-4">
                  <CheckCircle className="w-8 h-8 text-emerald-600" />
                </div>
              )}
              {(state === 'expired' || state === 'invalid') && (
                <div className="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-4">
                  <XCircle className="w-8 h-8 text-red-600" />
                </div>
              )}

              <CardTitle className="text-2xl text-center">
                {state === 'sent' && 'Check Your Email'}
                {state === 'verifying' && 'Verifying...'}
                {state === 'verified' && 'Email Verified!'}
                {state === 'expired' && 'Link Expired'}
                {state === 'invalid' && 'Invalid Link'}
              </CardTitle>
              
              <p className="text-center text-gray-600 text-sm mt-2">
                {state === 'sent' && (
                  <>We've sent a verification link to <strong>{email}</strong></>
                )}
                {state === 'verifying' && 'Please wait while we verify your email...'}
                {state === 'verified' && 'Your email has been successfully verified'}
                {state === 'expired' && 'This verification link has expired'}
                {state === 'invalid' && 'This verification link is invalid'}
              </p>
            </div>
          </CardHeader>
          <CardContent className="space-y-4">
            {state === 'sent' && (
              <>
                <div className="bg-gray-50 rounded-lg p-4 space-y-2">
                  <p className="text-sm font-medium text-gray-900">Next steps:</p>
                  <ol className="text-sm text-gray-600 space-y-1 list-decimal list-inside">
                    <li>Open your email inbox</li>
                    <li>Click the verification link we sent</li>
                    <li>You'll be automatically redirected</li>
                  </ol>
                </div>

                <Button
                  onClick={simulateVerification}
                  className="w-full bg-emerald-600 hover:bg-emerald-700"
                >
                  I've Clicked the Link (Simulate)
                </Button>

                <div className="text-center pt-4">
                  <p className="text-sm text-gray-600 mb-3">
                    Didn't receive the email?
                  </p>
                  <Button
                    onClick={handleResend}
                    variant="outline"
                    disabled={!canResend}
                    className="w-full"
                  >
                    <RefreshCw className="w-4 h-4 mr-2" />
                    {canResend ? 'Resend Verification Email' : `Resend in ${countdown}s`}
                  </Button>
                </div>
              </>
            )}

            {state === 'verifying' && (
              <div className="text-center py-4">
                <Loader2 className="w-8 h-8 text-emerald-600 animate-spin mx-auto" />
              </div>
            )}

            {state === 'verified' && (
              <div className="text-center py-4">
                <p className="text-sm text-gray-600">
                  Redirecting you to your dashboard...
                </p>
              </div>
            )}

            {(state === 'expired' || state === 'invalid') && (
              <>
                <p className="text-sm text-gray-600 text-center">
                  {state === 'expired' 
                    ? 'Your verification link has expired. Request a new one to continue.'
                    : 'The verification link appears to be invalid. Please try again.'
                  }
                </p>
                <Button
                  onClick={handleResend}
                  className="w-full bg-emerald-600 hover:bg-emerald-700"
                >
                  <RefreshCw className="w-4 h-4 mr-2" />
                  Request New Link
                </Button>
              </>
            )}
          </CardContent>
        </Card>

        <div className="mt-6 text-center">
          <p className="text-sm text-gray-600">
            Need help?{" "}
            <a href="#" className="text-emerald-600 hover:underline font-medium">
              Contact Support
            </a>
          </p>
        </div>
      </div>
    </div>
  );
}
