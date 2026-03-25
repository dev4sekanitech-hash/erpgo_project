import { useEffect } from 'react';
import { router, usePage } from '@inertiajs/react';

// This page acts as a routing guard. The server redirects company users to
// /account (Account module dashboard). This client-side redirect is a safety
// net in case the server-side redirect does not fire (e.g. package not loaded).
export default function Dashboard() {
    const { auth } = usePage().props as any;
    const userType = auth?.user?.type;

    useEffect(() => {
        if (userType === 'company' || userType === 'staff' || userType === 'vendor' || userType === 'client') {
            router.visit('/account', { replace: true });
        }
    }, [userType]);

    return null;
}
