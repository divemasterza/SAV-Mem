<?php
if (!defined('ABSPATH')) exit;

function generate_certificate($member_id, $orientation = 'portrait') {
    require_once(plugin_dir_path(__FILE__) . '../vendor/setasign/fpdf/fpdf.php');
    
    $member = get_member_details($member_id);
    $template_path = get_certificate_template($orientation);
    
    if (!file_exists($template_path)) {
        return false;
    }
    
    $pdf = new FPDF();
    $pdf->AddPage($orientation === 'portrait' ? 'P' : 'L');
    $pdf->Image($template_path, 0, 0, $pdf->GetPageWidth(), $pdf->GetPageHeight());
    
    // Add company name to certificate
    $pdf->SetFont('Arial', 'B', 24);
    $pdf->SetXY(0, $orientation === 'portrait' ? 100 : 80);
    $pdf->Cell($pdf->GetPageWidth(), 20, $member->company_name, 0, 1, 'C');
    
    $output_path = wp_upload_dir()['path'] . '/certificate-' . $member_id . '-' . $orientation . '.pdf';
    $pdf->Output('F', $output_path);
    
    return $output_path;
}

function get_certificate_template($orientation) {
    $option_name = 'savrala_certificate_template_' . $orientation;
    $template_id = get_option($option_name);
    return get_attached_file($template_id);
}

function get_certificate_url($orientation) {
    if (!is_user_logged_in()) return '';
    
    $user_id = get_current_user_id();
    $member = get_member_details($user_id);
    
    if ($member->status !== 'active') return '';
    
    $certificate_path = generate_certificate($user_id, $orientation);
    return wp_upload_dir()['url'] . '/certificate-' . $user_id . '-' . $orientation . '.pdf';
}