<script>    

    // ========================== Export Js Start ==============================
            document.addEventListener('DOMContentLoaded', function() {
                const exportOptions = document.getElementById('exportOptions');
                
                if (exportOptions) {
                    exportOptions.addEventListener('change', function() {
                        const format = this.value;
                        const table = document.getElementById('assignmentTable');
                        let data = [];
                        const headers = [];
    
                        // Get the table headers
                        table.querySelectorAll('thead th').forEach(th => {
                            headers.push(th.innerText.trim());
                        });
    
                        // Get the table rows
                        table.querySelectorAll('tbody tr').forEach(tr => {
                            const row = {};
                            tr.querySelectorAll('td').forEach((td, index) => {
                                row[headers[index]] = td.innerText.trim();
                            });
                            data.push(row);
                        });
    
                        if (format === 'csv') {
                            downloadCSV(data);
                        } else if (format === 'json') {
                            downloadJSON(data);
                        }
                    });
                }
            });
    
            function downloadCSV(data) {
                const csv = data.map(row => Object.values(row).join(',')).join('\n');
                const blob = new Blob([csv], { type: 'text/csv' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'download.csv';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            }
    
            function downloadJSON(data) {
                const json = JSON.stringify(data, null, 2);
                const blob = new Blob([json], { type: 'application/json' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'download.json';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            }
            // ========================== Export Js End ==============================
        
            // Table Header Checkbox checked all js Start
            $('#selectAll').on('change', function () {
                $('.form-check .form-check-input').prop('checked', $(this).prop('checked')); 
            }); 
        
            // Data Tables
            new DataTable('#assignmentTable', {
                searching: true,
                lengthChange: false,
                info: true,   // Bottom Left Text => Showing 1 to 10 of 12 entries
                paging: true,
               
            });

            $(document).ready( function () {
    $('#myTable').DataTable({
        lengthChange: false,
       
    });
} );
        </script>