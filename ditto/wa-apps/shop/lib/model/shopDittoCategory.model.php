<?php
/**
 *
 * @author WebAsyst Team
 * @version SVN: $Id: shopCategory.model.php 2026 2012-08-14 14:39:28Z vlad $
 */
class shopDittoCategoryModel extends waNestedSetModel
{
    protected $table = 'shop_category';

    protected $left = 'left_key';
    protected $right = 'right_key';
    protected $parent = 'parent_id';

    const TYPE_STATIC = 1;
    const TYPE_DYNAMIC = 0;

    public function getTree($id, $depth = null, $where = Array())
    {
        $data = parent::getTree($id, $depth, $where);

        /*if (count($where))
        {
            foreach ($data as &$item)
            {
                $item['url'] = wa()->getRouteUrl('shop/frontend/category', array('category_url' => waRequest::param('url_type') == 1 ? $item['url'] : $item['full_url']));
            }
            unset($item);
        }*/

        return $data;
    }

    public function getAliasFeature($alias, $category, $limit)
    {
        $data = Array();

        $category = implode(",",$category);

        if($category)
        {
        	$sql = "
	        SELECT
	        	prod.id,
	        	feat_prod.feature_value_id,
	        	feat.name,
	        	feat.code,
	        	feat_name.value
	        FROM
	        	shop_category_products as cat_prod,
	        	shop_product as prod,
	        	shop_product_features as feat_prod,
	        	shop_feature as feat,
	        	shop_feature_values_varchar as feat_name
	        where
	        	cat_prod.category_id in (".$category.") and
	        	prod.id = cat_prod.product_id and
	        	feat_prod.product_id = prod.id and
	        	feat_prod.feature_id = feat.id and
	        	feat.code = '".$alias."' and
	        	feat.status = 'public' and
	        	feat_name.id = feat_prod.feature_value_id
	        group by feat_prod.feature_value_id
	        order by feat_name.sort asc
	        limit 0, ".$limit."
	        ";

	        $data = $this->query($sql)->fetchAll();
        }

        return $data;
    }

}
