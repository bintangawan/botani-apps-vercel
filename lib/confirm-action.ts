"use client";

type ConfirmActionOptions = {
  title: string;
  text: string;
  confirmText: string;
  cancelText?: string;
  variant?: "primary" | "danger";
};

export async function confirmAction({
  title,
  text,
  confirmText,
  cancelText = "Batal",
  variant = "primary",
}: ConfirmActionOptions): Promise<boolean> {
  const { default: Swal } = await import("sweetalert2");
  const result = await Swal.fire({
    titleText: title,
    text,
    icon: variant === "danger" ? "warning" : "question",
    iconColor: variant === "danger" ? "#e11d48" : "#059669",
    showCancelButton: true,
    reverseButtons: true,
    focusCancel: true,
    returnFocus: true,
    heightAuto: false,
    confirmButtonText: confirmText,
    cancelButtonText: cancelText,
    buttonsStyling: false,
    customClass: {
      container: "botani-swal-container",
      popup: "botani-swal-popup",
      title: "botani-swal-title",
      htmlContainer: "botani-swal-text",
      actions: "botani-swal-actions",
      confirmButton: `botani-swal-confirm${variant === "danger" ? " botani-swal-confirm-danger" : ""}`,
      cancelButton: "botani-swal-cancel",
    },
  });

  return result.isConfirmed;
}
