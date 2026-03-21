import { useState } from "react";
import { Head, usePage, router } from "@inertiajs/react";
import { useTranslation } from "react-i18next";
import { useDeleteHandler } from "@/hooks/useDeleteHandler";
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Button } from "@/components/ui/button";
import { Card, CardContent } from "@/components/ui/card";
import { DataTable } from "@/components/ui/data-table";
import { ConfirmationDialog } from "@/components/ui/confirmation-dialog";
import { Plus, Eye, Edit, Trash2, Briefcase } from "lucide-react";
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from "@/components/ui/tooltip";
import NoRecordsFound from "@/components/no-records-found";
import { StarrlightJobsProps, Job } from "../types";

export default function JobsIndex() {
    const { t } = useTranslation();
    const { jobs, auth } = usePage<StarrlightJobsProps>().props;

    const { deleteState, openDeleteDialog, closeDeleteDialog, confirmDelete } =
        useDeleteHandler({
            routeName: "starrlight.jobs.destroy",
            defaultMessage: t("Are you sure you want to delete this job?"),
        });

    const tableColumns = [
        {
            key: "title",
            header: t("Title"),
        },
        {
            key: "job_type",
            header: t("Job Type"),
        },
        {
            key: "shift_pattern",
            header: t("Shift Pattern"),
        },
        {
            key: "city",
            header: t("City"),
        },
        {
            key: "province",
            header: t("Province"),
        },
        {
            key: "is_active",
            header: t("Status"),
            render: (value: boolean) => (
                <span
                    className={`px-2 py-1 rounded-full text-xs ${
                        value
                            ? "bg-green-100 text-green-800"
                            : "bg-red-100 text-red-800"
                    }`}
                >
                    {value ? t("Active") : t("Inactive")}
                </span>
            ),
        },
        {
            key: "actions",
            header: t("Actions"),
            render: (_: any, record: Job) => (
                <div className="flex gap-1">
                    <TooltipProvider>
                        <Tooltip delayDuration={0}>
                            <TooltipTrigger asChild>
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    onClick={() =>
                                        router.get(
                                            route(
                                                "starrlight.jobs.show",
                                                record.id,
                                            ),
                                        )
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
                            "manage-starrlight-jobs",
                        ) && (
                            <>
                                <Tooltip delayDuration={0}>
                                    <TooltipTrigger asChild>
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            onClick={() =>
                                                router.get(
                                                    route(
                                                        "starrlight.jobs.edit",
                                                        record.id,
                                                    ),
                                                )
                                            }
                                            className="h-8 w-8 p-0 text-orange-600 hover:text-orange-700"
                                        >
                                            <Edit className="h-4 w-4" />
                                        </Button>
                                    </TooltipTrigger>
                                    <TooltipContent>
                                        <p>{t("Edit")}</p>
                                    </TooltipContent>
                                </Tooltip>
                                <Tooltip delayDuration={0}>
                                    <TooltipTrigger asChild>
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            onClick={() =>
                                                openDeleteDialog(record.id)
                                            }
                                            className="h-8 w-8 p-0 text-destructive hover:text-destructive"
                                        >
                                            <Trash2 className="h-4 w-4" />
                                        </Button>
                                    </TooltipTrigger>
                                    <TooltipContent>
                                        <p>{t("Delete")}</p>
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
            breadcrumbs={[{ label: t("Starrlight") }, { label: t("Jobs") }]}
            pageTitle={t("Manage Jobs")}
            pageActions={
                auth.user?.permissions?.includes("manage-starrlight-jobs") && (
                    <Button
                        size="sm"
                        onClick={() =>
                            router.get(route("starrlight.jobs.create"))
                        }
                    >
                        <Plus className="h-4 w-4" />
                    </Button>
                )
            }
        >
            <Head title={t("Jobs")} />

            <Card className="shadow-sm">
                <CardContent className="p-0">
                    <DataTable
                        data={jobs.data}
                        columns={tableColumns}
                        emptyState={
                            <NoRecordsFound
                                icon={Briefcase}
                                title={t("No jobs found")}
                                description={t(
                                    "Job listings will appear here.",
                                )}
                                className="h-auto"
                            />
                        }
                    />
                </CardContent>
            </Card>

            <ConfirmationDialog
                open={deleteState.isOpen}
                onClose={closeDeleteDialog}
                onConfirm={confirmDelete}
                title={t("Delete Job")}
                message={deleteState.message}
            />
        </AuthenticatedLayout>
    );
}
