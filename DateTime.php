class DateFormatter {
    public static function formatTurkishDate($dateString) {
        $date = new DateTime($dateString);
        $formattedDate = $date->format('d F Y, H:i');

        $turkishMonths = [
            'January' => 'Ocak',
            'February' => 'Şubat',
            'March' => 'Mart',
            'April' => 'Nisan',
            'May' => 'Mayıs',
            'June' => 'Haziran',
            'July' => 'Temmuz',
            'August' => 'Ağustos',
            'September' => 'Eylül',
            'October' => 'Ekim',
            'November' => 'Kasım',
            'December' => 'Aralık'
        ];

        foreach ($turkishMonths as $english => $turkish) {
            $formattedDate = str_replace($english, $turkish, $formattedDate);
        }

        return $formattedDate;
    }
}
