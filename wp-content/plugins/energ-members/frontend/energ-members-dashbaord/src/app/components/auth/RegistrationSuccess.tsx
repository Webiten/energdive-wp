import { Card, CardContent, CardHeader, CardTitle } from "../ui/card";
import { Button } from "../ui/button";
import { CheckCircle, Clock, Mail } from "lucide-react";

interface RegistrationSuccessProps {
  requiresApproval: boolean;
  onContinue: () => void;
}

export function RegistrationSuccess({ requiresApproval, onContinue }: RegistrationSuccessProps) {
  return (
    <div className="min-h-screen flex items-center justify-center bg-white">
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
              <div className={`w-16 h-16 ${requiresApproval ? 'bg-blue-100' : 'bg-emerald-100'} rounded-full flex items-center justify-center mb-4`}>
                {requiresApproval ? (
                  <Clock className="w-8 h-8 text-blue-600" />
                ) : (
                  <CheckCircle className="w-8 h-8 text-emerald-600" />
                )}
              </div>

              <CardTitle className="text-2xl text-center">
                {requiresApproval ? 'Registration Submitted!' : 'Welcome to ENERGCLUB!'}
              </CardTitle>
              
              <p className="text-center text-gray-600 text-sm mt-2">
                {requiresApproval 
                  ? 'Your profile has been created successfully'
                  : 'Your account is ready to use'
                }
              </p>
            </div>
          </CardHeader>
          <CardContent className="space-y-6">
            {requiresApproval ? (
              <>
                <div className="bg-blue-50 border border-blue-200 rounded-lg p-4 space-y-3">
                  <div className="flex items-start gap-3">
                    <Clock className="w-5 h-5 text-blue-600 mt-0.5" />
                    <div>
                      <h4 className="font-semibold text-blue-900 mb-1">Pending Admin Approval</h4>
                      <p className="text-sm text-blue-800">
                        Your registration is currently under review. Our team typically processes new accounts within 24-48 hours.
                      </p>
                    </div>
                  </div>
                </div>

                <div className="space-y-3">
                  <h4 className="font-semibold text-gray-900">What happens next?</h4>
                  <ol className="space-y-2 text-sm text-gray-600">
                    <li className="flex items-start gap-2">
                      <span className="font-semibold text-emerald-600 mt-0.5">1.</span>
                      <span>Our admin team will review your profile information</span>
                    </li>
                    <li className="flex items-start gap-2">
                      <span className="font-semibold text-emerald-600 mt-0.5">2.</span>
                      <span>You'll receive an email notification once approved</span>
                    </li>
                    <li className="flex items-start gap-2">
                      <span className="font-semibold text-emerald-600 mt-0.5">3.</span>
                      <span>Access your personalized dashboard and all platform features</span>
                    </li>
                  </ol>
                </div>

                <div className="bg-gray-50 rounded-lg p-4 flex items-start gap-3">
                  <Mail className="w-5 h-5 text-gray-600 mt-0.5" />
                  <div>
                    <p className="text-sm text-gray-900 font-medium mb-1">
                      Check your email
                    </p>
                    <p className="text-sm text-gray-600">
                      We've sent a confirmation to your email address. You'll receive another email once your account is approved.
                    </p>
                  </div>
                </div>
              </>
            ) : (
              <>
                <div className="bg-emerald-50 border border-emerald-200 rounded-lg p-4">
                  <p className="text-sm text-emerald-900">
                    Your personalized experience is ready, with intelligence modules tailored to your professional interests and industry focus available whenever you choose to access them.
                  </p>
                </div>

                {/* <div className="space-y-3">
                  <h4 className="font-semibold text-gray-900">You now have access to:</h4>
                  <ul className="space-y-2 text-sm text-gray-600">
                    <li className="flex items-center gap-2">
                      <CheckCircle className="w-4 h-4 text-emerald-600" />
                      <span>Personalized intelligence feed</span>
                    </li>
                    <li className="flex items-center gap-2">
                      <CheckCircle className="w-4 h-4 text-emerald-600" />
                      <span>Industry-specific reports and analysis</span>
                    </li>
                    <li className="flex items-center gap-2">
                      <CheckCircle className="w-4 h-4 text-emerald-600" />
                      <span>Community discussions and expert insights</span>
                    </li>
                    <li className="flex items-center gap-2">
                      <CheckCircle className="w-4 h-4 text-emerald-600" />
                      <span>Events, webinars, and networking opportunities</span>
                    </li>
                  </ul>
                </div> */}

                <Button 
                  onClick={onContinue}
                  className="w-full bg-emerald-600 hover:bg-emerald-700"
                >
                  Go to Dashboard
                </Button>
              </>
            )}

            {requiresApproval && (
              <Button 
                onClick={onContinue}
                variant="outline"
                className="w-full"
              >
                Return to Home
              </Button>
            )}
          </CardContent>
        </Card>

        {/* <div className="mt-6 text-center">
          <p className="text-sm text-gray-600">
            Questions about your account?{" "}
            <a href="#" className="text-emerald-600 hover:underline font-medium">
              Contact Support
            </a>
          </p>
        </div> */}
      </div>
    </div>
  );
}
