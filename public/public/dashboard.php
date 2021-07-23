<?php 
$this->load->view('header');
$this->load->view('sidebar');
$this->load->view($page);
$this->load->view('footer');
?>

<script>
    $(document).ready(function(){
        $("select").each(function(){
            $(this).wrap("<span class='select-wrapper'></span>");
            $(this).after("<span class='holder'></span>");
        });
        $("select").change(function(){
            var selectedOption = $(this).find(":selected").text();
            $(this).next(".holder").text(selectedOption);
        }).trigger('change');
    })
</script>