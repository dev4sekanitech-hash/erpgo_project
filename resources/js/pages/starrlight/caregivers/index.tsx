import { useState } from "react";
import { Head, usePage, router } from "@inertiajs/react";
import { useTranslation } from "react-i18next";
import { useDeleteHandler } from "@/hooks/useDeleteHandler";
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Button } from "@/components/ui/button";
import { Card, CardContent } from "@/components/ui/card";
import { DataTable } from "@/components/ui/data-table";
import { ConfirmationDialog } from "@/components/ui/confirmation-dialog";
import { Plus, Eye, Trash2, UserCheck } from "lucide-react";
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from "@/components/ui/tooltip";
import NoRecordsFound from "@/components/no-records-found";
import { StarrlightCaregiversProps, CaregiverProfile } from "../types";

export default function CaregiversIndex() {
    const { t } = useTranslation();
    const { caregivers, auth } = usePage<StarrlightCaregiversProps>().props;

    const { deleteState, openDeleteDialog, closeDeleteDialog, confirmDelete } =
        useDeleteHandler({
            routeName: "starrlight.caregivers.destroy",
            defaultMessage: t(
                "Are you sure you want to delete this caregiver profile?",
            ),
        });

    const tableColumns = [
        {
            key: "name",
            header: t("Name"),
            render: (_: any, record: CaregiverProfile) => (
                <span>
                    {record.first_name} {record.last_name}
                </span>
            ),
        },
        {
            key: "email",
            header: t("Email"),
        },
        {
            key: "phone",
            header: t("Phone"),
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
            render: (_: any, record: CaregiverProfile) => (
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
                                                "starrlight.caregivers.show",
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
                            "manage-starrlight-caregivers",
                        ) && (
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
                { label: t("Caregivers") },
            ]}
            pageTitle={t("Manage Caregivers")}
        >
            <Head title={t("Caregivers")} />

            <Card className="shadow-sm">
                <CardContent className="p-0">
                    <DataTable
                        data={caregivers.data}
                        columns={tableColumns}
                        emptyState={
                            <NoRecordsFound
                                icon={UserCheck}
                                title={t("No caregivers found")}
                                description={t(
                                    "Caregiver profiles will appear here.",
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
                title={t("Delete Caregiver")}
                message={deleteState.message}
            />
        </AuthenticatedLayout>
    );
}
