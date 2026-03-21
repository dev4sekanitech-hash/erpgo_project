import { Head, usePage, router } from "@inertiajs/react";
import { useTranslation } from "react-i18next";
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Card, CardContent } from "@/components/ui/card";
import { DataTable } from "@/components/ui/data-table";
import { Button } from "@/components/ui/button";
import { Eye, Check, X, FileText } from "lucide-react";
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from "@/components/ui/tooltip";
import NoRecordsFound from "@/components/no-records-found";
import { StarrlightStaffRequestsProps, StaffRequest } from "../types";

export default function StaffRequestsIndex() {
    const { t } = useTranslation();
    const { requests, auth } = usePage<StarrlightStaffRequestsProps>().props;

    const handleApprove = (id: number) => {
        router.post(route("starrlight.staff-requests.approve", id));
    };

    const handleReject = (id: number) => {
        router.post(route("starrlight.staff-requests.reject", id));
    };

    const tableColumns = [
        {
            key: "facility_name",
            header: t("Facility Name"),
        },
        {
            key: "contact_name",
            header: t("Contact Name"),
        },
        {
            key: "email",
            header: t("Email"),
        },
        {
            key: "staff_needed",
            header: t("Staff Needed"),
        },
        {
            key: "start_date",
            header: t("Start Date"),
        },
        {
            key: "status",
            header: t("Status"),
            render: (value: string) => (
                <span
                    className={`px-2 py-1 rounded-full text-xs ${
                        value === "approved"
                            ? "bg-green-100 text-green-800"
                            : value === "pending"
                              ? "bg-yellow-100 text-yellow-800"
                              : value === "rejected"
                                ? "bg-red-100 text-red-800"
                                : "bg-gray-100 text-gray-800"
                    }`}
                >
                    {value || "pending"}
                </span>
            ),
        },
        {
            key: "actions",
            header: t("Actions"),
            render: (_: any, record: StaffRequest) => (
                <div className="flex gap-1">
                    <TooltipProvider>
                        <Tooltip delayDuration={0}>
                            <TooltipTrigger asChild>
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    onClick={() =>
                                        (window.location.href = route(
                                            "starrlight.staff-requests.show",
                                            record.id,
                                        ))
                                    }
                                    className="h-8 w-8 p-0 text-blue-600 hover:text-blue-700"
                                >
                                    <Eye className="h-4 w-4" />
                                </Button>
                            </TooltipTrigger>
                            <TooltipContent>
                                <p>{t("View")}</p>
                            </TooltipContent>
                        </Tooltip>
                        {auth.user?.permissions?.includes(
                            "manage-starrlight-staff-requests",
                        ) &&
                            record.status === "pending" && (
                                <>
                                    <Tooltip delayDuration={0}>
                                        <TooltipTrigger asChild>
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                onClick={() =>
                                                    handleApprove(record.id)
                                                }
                                                className="h-8 w-8 p-0 text-green-600 hover:text-green-700"
                                            >
                                                <Check className="h-4 w-4" />
                                            </Button>
                                        </TooltipTrigger>
                                        <TooltipContent>
                                            <p>{t("Approve")}</p>
                                        </TooltipContent>
                                    </Tooltip>
                                    <Tooltip delayDuration={0}>
                                        <TooltipTrigger asChild>
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                onClick={() =>
                                                    handleReject(record.id)
                                                }
                                                className="h-8 w-8 p-0 text-red-600 hover:text-red-700"
                                            >
                                                <X className="h-4 w-4" />
                                            </Button>
                                        </TooltipTrigger>
                                        <TooltipContent>
                                            <p>{t("Reject")}</p>
                                        </TooltipContent>
                                    </Tooltip>
                                </>
                            )}
                    </TooltipProvider>
                </div>
            ),
        },
    ];

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { label: t("Starrlight") },
                { label: t("Staff Requests") },
            ]}
            pageTitle={t("Manage Staff Requests")}
        >
            <Head title={t("Staff Requests")} />

            <Card className="shadow-sm">
                <CardContent className="p-0">
                    <DataTable
                        data={requests.data}
                        columns={tableColumns}
                        emptyState={
                            <NoRecordsFound
                                icon={FileText}
                                title={t("No staff requests found")}
                                description={t(
                                    "Staff requests will appear here.",
                                )}
                                className="h-auto"
                            />
                        }
                    />
                </CardContent>
            </Card>
        </AuthenticatedLayout>
    );
}
