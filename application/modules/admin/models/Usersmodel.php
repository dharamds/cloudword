<?php

class Usersmodel extends CI_Model 
{
    function __construct() {
        parent::__construct(); 
        
    }

    function allusers_count()
    {   
        $query = $this
                ->db
                ->where('client_id > 1')->get('client');
        return $query->num_rows();  

    }
    
    function allusers($limit,$start,$col,$dir)
    {   
       $query = $this
                ->db
                ->limit($limit,$start)
                ->order_by($col,$dir)
                ->where('client_id > 1')->get('client');
        
        if($query->num_rows()>0)
        {
            return $query->result(); 
        }
        else
        {
            return null;
        }
        
    }
   
    function users_search($limit,$start,$search,$col,$dir)
    {
        $query = $this
                ->db
                ->like('client_id',$search)
                ->or_like('email',$search)
                ->limit($limit,$start)
                ->order_by($col,$dir)
                ->where('client_id > 1')->get('client');
        
       
        if($query->num_rows()>0)
        {
            return $query->result();  
        }
        else
        {
            return null;
        }
    }

    function allusers_search_count($search)
    {
        $query = $this
                ->db
                ->like('client_id',$search)
                ->or_like('email',$search)
                ->where('client_id > 1')->get('client');
    
        return $query->num_rows();
    } 
   
}
