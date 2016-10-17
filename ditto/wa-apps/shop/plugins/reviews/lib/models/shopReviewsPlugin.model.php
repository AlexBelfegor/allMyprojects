<?php
class shopReviewsPluginModel extends waModel
{
    const STATUS_DELETED   = 'deleted';
    const STATUS_PUBLISHED = 'approved';
    protected $table = 'shop_product_reviews';

    public function GetLastReviews($limit)
    {
        $sql = "
        SELECT
        	review.id,
        	review.product_id,
        	review.datetime,
        	review.title,
        	review.text,
        	review.name,
        	review.email
        FROM
        	".$this->table." as review
        where
        	review.status = 'approved'
        order by review.datetime desc
        limit ".$limit."
        ";

        return $this->query($sql)->fetchAll();
    }

    public function getList($offset = 0, $count = null, array $options = array(),$product_id = null)
    {
        if (!empty($options['reply_to'])) {
            $sql = "SELECT *, p.text AS parent_text, parent_datetime FROM {$this->table} r LEFT JOIN {$this->table} p ON r.parent_id = p.id";
        } else {
            $sql = "SELECT * FROM {$this->table} ";
        }

        $where = array();
        if ($product_id) {
            $where[] = "product_id = ".(int)$product_id;
        }

        if (wa()->getEnv() == 'frontend') {
            $where[] = "review_id = 0 and parent_id = 0";
            $where[] = "status = '".self::STATUS_PUBLISHED."'";
        }
        if ($where) {
            $sql .= " WHERE ".implode(' AND ', $where);
        }

        $sql .= " ORDER BY datetime";
        if ($count) {
            $sql .= " DESC LIMIT ".(int)$offset.",".(int)$count;
        }

        $data =  $this->query($sql)->fetchAll('id');
        $this->extendItems($data, $options);

        return $data;
    }

    public function GetAdminAnswers($id)
    {
        $sql = "
        SELECT
        	review.id,
        	review.product_id,
        	review.datetime,
        	review.title,
        	review.text,
        	review.name,
        	review.email,
        	review.contact_id,
        	contact.firstname,
        	contact.photo,
        	prod.image_id,
        	prod.ext
        FROM
        	wa_contact as contact,
        	".$this->table." as review
        		left join shop_product as prod on (prod.id = review.contact_id)
        where
        	contact.id = review.contact_id and
        	review.status = 'approved' and
        	review.review_id = ".$id."
        order by review.datetime desc
        ";

        return $this->query($sql)->fetchAll();
    }

    public function count($product_id = null, $reviews_only = true)
    {
        $sql = "SELECT COUNT(id) AS cnt FROM `{$this->table}` ";

        $where = array();
        if ($product_id) {
            $where[] = "product_id = ".(int)$product_id;
        }
        if ($reviews_only) {
            $where[] = "review_id = 0";
        }
        if (wa()->getEnv() == 'frontend') {
            $where[] = "status = '".self::STATUS_PUBLISHED."'";
        }
        if ($where) {
            $sql .= " WHERE ".implode(' AND ', $where);
        }

        return $this->query($sql)->fetchField('cnt');
    }

    /**
     * @param int|array $contact_id
     */
    static public function getAuthorInfo($contact_id)
    {
        $fields = 'id,name,photo_url_50,photo_url_20';
        $contact_ids = (array)$contact_id;
        $collection = new waContactsCollection('id/'.implode(',', $contact_ids));
        $contacts = $collection->getContacts($fields, 0, count($contact_ids));
        if (is_numeric($contact_id)) {
            if (isset($contacts[$contact_id])) {
                return $contacts[$contact_id];
            } else {
                return array_fill_keys(explode(',', $fields), '');
            }
        } else {
            return $contacts;
        }
    }

    private function extendItems(&$items, array $options = array())
    {
        $escape = !empty($options['escape']);

        $contact_ids = array();
        foreach ($items as $item) {
            if ($item['contact_id']) {
                $contact_ids[] = $item['contact_id'];
            }
        }
        $contact_ids = array_unique($contact_ids);
        $contacts = self::getAuthorInfo($contact_ids);

        foreach ($items as &$item) {
            $item['datetime_ts'] = strtotime($item['datetime']);
            $author = array(
                'name' =>  $item['name'],
                'email' => $item['email'],
                'site' =>  $item['site']
            );
            $item['author'] = array_merge(
                $author,
                isset($contacts[$item['contact_id']]) ? $contacts[$item['contact_id']] : array()
            );
            if ($escape) {
                $item['author']['name'] = htmlspecialchars($item['author']['name']);
                $item['text'] = nl2br(htmlspecialchars($item['text']));
                $item['title'] = htmlspecialchars($item['title']);
            }
            // recursive workuping
            if (!empty($item['comments'])) {
                $this->extendItems($item['comments'], $options);
            }
        }

        unset($item);
    }

	public function UpdateCntReview($id_review,$field,$count)
	{
		$sql = "UPDATE ".$this->table." SET ".$field." = ".$field." + 1 WHERE id=".$id_review."";
		$this->query($sql);
	}
}

