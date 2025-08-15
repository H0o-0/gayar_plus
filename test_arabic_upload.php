<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اختبار رفع Excel العربي</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .arabic-text { font-size: 1.1em; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">🧪 اختبار قارئ Excel العربي</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h5>📋 تعليمات الاختبار:</h5>
                            <ul>
                                <li>ارفع ملف Excel يحتوي على نصوص عربية</li>
                                <li>تأكد من أن الملف يحتوي على عمودين: الأسماء والأسعار</li>
                                <li>النظام سيحاول قراءة الملف بعدة طرق مختلفة</li>
                                <li>ستظهر رسائل تشخيصية مفصلة</li>
                            </ul>
                        </div>

                        <form id="test-form" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="excel-file" class="form-label">اختر ملف Excel:</label>
                                <input type="file" class="form-control" id="excel-file" name="excel_file" accept=".xlsx,.xls,.csv" required>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <label for="name-column" class="form-label">عمود الأسماء:</label>
                                    <select class="form-control" id="name-column" name="name_column">
                                        <option value="0">العمود الأول (A)</option>
                                        <option value="1">العمود الثاني (B)</option>
                                        <option value="2">العمود الثالث (C)</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="price-column" class="form-label">عمود الأسعار:</label>
                                    <select class="form-control" id="price-column" name="price_column">
                                        <option value="1">العمود الثاني (B)</option>
                                        <option value="0">العمود الأول (A)</option>
                                        <option value="2">العمود الثالث (C)</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="start-row" class="form-label">بداية البيانات:</label>
                                    <select class="form-control" id="start-row" name="start_row">
                                        <option value="1">الصف الأول</option>
                                        <option value="2" selected>الصف الثاني</option>
                                        <option value="3">الصف الثالث</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary btn-lg">🚀 اختبار القراءة</button>
                                <button type="button" class="btn btn-secondary" onclick="testWithSample()">📄 اختبار مع ملف تجريبي</button>
                            </div>
                        </form>

                        <div id="results" class="mt-4" style="display: none;">
                            <h4>📊 نتائج الاختبار:</h4>
                            <div id="results-content"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $('#test-form').on('submit', function(e) {
            e.preventDefault();
            
            var formData = new FormData(this);
            formData.append('import_batch', 'test_' + Date.now());
            
            $('#results').show();
            $('#results-content').html('<div class="alert alert-info">🔄 جاري معالجة الملف...</div>');
            
            $.ajax({
                url: 'admin/warehouse/process_upload.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#results-content').html(response);
                },
                error: function(xhr, status, error) {
                    $('#results-content').html('<div class="alert alert-danger">❌ خطأ: ' + error + '</div>');
                }
            });
        });

        function testWithSample() {
            // إنشاء ملف تجريبي
            var csvContent = "اسم المنتج,السعر\n";
            csvContent += "شاشة آيفون 13,250000\n";
            csvContent += "غطاء سامسونج جالاكسي,25000\n";
            csvContent += "بطارية هواوي P30,80000\n";
            csvContent += "كابل شحن آيفون,45000\n";
            
            var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            var file = new File([blob], "test_arabic.csv", { type: "text/csv" });
            
            // إنشاء FormData مع الملف التجريبي
            var formData = new FormData();
            formData.append('excel_file', file);
            formData.append('name_column', '0');
            formData.append('price_column', '1');
            formData.append('start_row', '2');
            formData.append('import_batch', 'test_sample_' + Date.now());
            
            $('#results').show();
            $('#results-content').html('<div class="alert alert-info">🔄 اختبار الملف التجريبي العربي...</div>');
            
            $.ajax({
                url: 'admin/warehouse/process_upload.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#results-content').html(response);
                },
                error: function(xhr, status, error) {
                    $('#results-content').html('<div class="alert alert-danger">❌ خطأ: ' + error + '</div>');
                }
            });
        }
    </script>
</body>
</html>
