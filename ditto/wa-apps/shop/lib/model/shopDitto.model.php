<?php
class shopDittoModel extends waModel
{
    public function GetLastArtcles($type_url,$limit)
    {
        $sql = "
        SELECT
        	post.id,
        	post.blog_id,
        	post.datetime,
        	post.title,
        	post.url,
        	post.text,
        	post.short_desc,
        	post.photo,
        	blog.name as name_blog,
        	blog.url as blog_url
        FROM
        	blog_post as post,
        	blog_blog as blog
        where
        	blog.status = 'public' and
        	blog.id = post.blog_id and
        	post.status = 'published'
        order by post.datetime desc
        limit ".$limit."
        ";

        return $this->query($sql)->fetchAll();
    }

    /*---*/
    public function getAllCategoryes()
    {
        $sql = "SELECT id,url,full_url,name FROM shop_category order by name asc, depth asc";
        $this->setCache(new waRuntimeCache('shop/categories/all'));
        return $this->query($sql)->fetchAll();
    }

    public function GetLastShopTableInfo()
    {
        $sql = "SHOW TABLE STATUS LIKE 'shop_order'";

        return $this->query($sql)->fetchAssoc();
    }

}
