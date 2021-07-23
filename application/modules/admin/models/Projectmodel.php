<?php

class Projectmodel extends MX_Model 
{
    function __construct() {
        parent::__construct(); 
        
    }

    function allprojects_count()
    {   
        $query = $this
                ->db
                ->get('project');
    
        return $query->num_rows();  

    }
    
    function allprojects($limit,$start,$col,$dir)
    {   
       $query = $this
                ->db
                ->limit($limit,$start)
                ->order_by($col,$dir)
                ->get('project');
        
        if($query->num_rows()>0)
        {
            return $query->result(); 
        }
        else
        {
            return null;
        }
        
    }
   
    function projects_search($limit,$start,$search,$col,$dir)
    {
        $query = $this
                ->db
                ->like('project_id',$search)
                ->or_like('project_name',$search)
                ->limit($limit,$start)
                ->order_by($col,$dir)
                ->get('project');
        
       
        if($query->num_rows()>0)
        {
            return $query->result();  
        }
        else
        {
            return null;
        }
    }

    function projects_search_count($search)
    {
        $query = $this
                ->db
                ->like('project_id',$search)
                ->or_like('project_name',$search)
                ->get('project');
    
        return $query->num_rows();
    } 
   
}
