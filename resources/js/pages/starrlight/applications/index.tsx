import { Head, usePage } from "@inertiajs/react";
import { useTranslation } from "react-i18next";
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Card, CardContent } from "@/components/ui/card";
import { DataTable } from "@/components/ui/data-table";
import { Eye, ClipboardList } from "lucide-react";
import { Button } from "@/components/ui/button";
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from "@/components/ui/tooltip";
import NoRecordsFound from "@/components/no-records-found";
import { StarrlightApplicationsProps, JobApplication } from "../types";

export default function ApplicationsIndex() {
    const { t } = useTranslation();
    const { applications, auth } = usePage<StarrlightApplicationsProps>().props;

    const tableColumns = [
        {
            key: "job.title",
            header: t("Job Title"),
            render: (_: any, record: JobApplication) => (
                <span>{record.job?.title || "-"}</span>
            ),
        },
        {
            key: "caregiver",
            header: t("Caregiver"),
            render: (_: any, record: JobApplication) => (
                <span>
                    {record.caregiver_profile?.first_name}{" "}
                    {record.caregiver_profile?.last_name}
                </span>
            ),
        },
        {
            key: "caregiver_profile.email",
            header: t("Email"),
            render: (_: any, record: JobApplication) => (
                <span>{record.caregiver_profile?.email || "-"}</span>
            ),
        },
        {
            key: "applied_at",
            header: t("Applied At"),
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
            render: (_: any, record: JobApplication) => (
                <div className="flex gap-1">
                    <TooltipProvider>
                        <Tooltip delayDuration={0}>
                            <TooltipTrigger asChild>
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    onClick={() =>
                                        (window.location.href = route(
                                            "starrlight.applications.show",
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
                    </TooltipProvider>
                </div>
            ),
        },
    ];

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { label: t("Starrlight") },
                { label: t("Applications") },
            ]}
            pageTitle={t("Manage Applications")}
        >
            <Head title={t("Applications")} />

            <Card className="shadow-sm">
                <CardContent className="p-0">
                    <DataTable
                        data={applications.data}
                        columns={tableColumns}
                        emptyState={
                            <NoRecordsFound
                                icon={ClipboardList}
                                title={t("No applications found")}
                                description={t(
                                    "Job applications will appear here.",
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
