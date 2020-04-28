<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 
 * Class Videos
 *
 * @since 2.0
 *
 * @Entity(table="videos")
 */
class Videos extends Model
{
    /**
     * 
     * @Id()
     * @Column()
     *
     * @var int
     */
    private $id;

    /**
     * 1:xvideos 2: pronoun
     *
     * @Column()
     *
     * @var int
     */
    private $type;

    /**
     * 影片名稱
     *
     * @Column()
     *
     * @var string
     */
    private $name;

    /**
     * 檔案名稱
     *
     * @Column(name="file_name", prop="fileName")
     *
     * @var string
     */
    private $fileName;

    /**
     * 原始檔案名稱
     *
     * @Column(name="origin_file_name", prop="originFileName")
     *
     * @var string|null
     */
    private $originFileName;

    /**
     * 已轉好的mp4影片完整路徑
     *
     * @Column(name="file_converted_mp4", prop="fileConvertedMp4")
     *
     * @var string|null
     */
    private $fileConvertedMp4;

    /**
     * 分類
     *
     * @Column()
     *
     * @var int
     */
    private $categories;

    /**
     * 是否下載完成 0: 未下載 1: 已下載
     *
     * @Column()
     *
     * @var int
     */
    private $download;

    /**
     * 原影片路徑
     *
     * @Column(name="origin_url", prop="originUrl")
     *
     * @var string
     */
    private $originUrl;

    /**
     * 1: url 解析異常 2: url 轉檔案異常 3: 影片下載中 4: 影片轉碼完成
     *
     * @Column()
     *
     * @var int
     */
    private $status;

    /**
     * 多解析度影片, 1080p代表其以下格式都有
     *
     * @Column(name="1080p", prop="db1080p")
     *
     * @var int|null
     */
    private $db1080p;

    /**
     * 封面圖
     *
     * @Column(name="cover_picture", prop="coverPicture")
     *
     * @var string|null
     */
    private $coverPicture;

    /**
     * 預覽圖
     *
     * @Column()
     *
     * @var string|null
     */
    private $preview;

    /**
     * 五秒影片
     *
     * @Column(name="five_second_video", prop="fiveSecondVideo")
     *
     * @var string|null
     */
    private $fiveSecondVideo;

    /**
     * 發布狀況 0:未發布 1:已發布
     *
     * @Column()
     *
     * @var int
     */
    private $release;

    /**
     * 影片撥放長度
     *
     * @Column()
     *
     * @var int|null
     */
    private $duration;

    /**
     * 撥放次數
     *
     * @Column(name="play_count", prop="playCount")
     *
     * @var int|null
     */
    private $playCount;

    /**
     * 點讚次數
     *
     * @Column(name="vote_count", prop="voteCount")
     *
     * @var int|null
     */
    private $voteCount;

    /**
     * 後台顯示單張封面圖
     *
     * @Column()
     *
     * @var int|null
     */
    private $cover;

    /**
     * 
     *
     * @Column(name="created_at", prop="createdAt")
     *
     * @var string|null
     */
    private $createdAt;

    /**
     * 
     *
     * @Column(name="updated_at", prop="updatedAt")
     *
     * @var string|null
     */
    private $updatedAt;

    /**
     * 爬蟲下載影片主機編號
     *
     * @Column(name="server_id", prop="serverId")
     *
     * @var int|null
     */
    private $serverId;

    /**
     * 是否要轉碼的影片
     *
     * @Column(name="is_transfer", prop="isTransfer")
     *
     * @var int
     */
    private $isTransfer;

    /**
     * 軟刪除 0: 默認值 1: 軟刪除
     *
     * @Column(name="soft_delete", prop="softDelete")
     *
     * @var int
     */
    private $softDelete;

    /**
     * 倒讚
     *
     * @Column()
     *
     * @var int
     */
    private $dislike;


    /**
     * @param int $id
     *
     * @return self
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @param int $type
     *
     * @return self
     */
    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param string $fileName
     *
     * @return self
     */
    public function setFileName(string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * @param string|null $originFileName
     *
     * @return self
     */
    public function setOriginFileName(?string $originFileName): self
    {
        $this->originFileName = $originFileName;

        return $this;
    }

    /**
     * @param string|null $fileConvertedMp4
     *
     * @return self
     */
    public function setFileConvertedMp4(?string $fileConvertedMp4): self
    {
        $this->fileConvertedMp4 = $fileConvertedMp4;

        return $this;
    }

    /**
     * @param int $categories
     *
     * @return self
     */
    public function setCategories(int $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * @param int $download
     *
     * @return self
     */
    public function setDownload(int $download): self
    {
        $this->download = $download;

        return $this;
    }

    /**
     * @param string $originUrl
     *
     * @return self
     */
    public function setOriginUrl(string $originUrl): self
    {
        $this->originUrl = $originUrl;

        return $this;
    }

    /**
     * @param int $status
     *
     * @return self
     */
    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @param int|null $db1080p
     *
     * @return self
     */
    public function setDb1080p(?int $db1080p): self
    {
        $this->db1080p = $db1080p;

        return $this;
    }

    /**
     * @param string|null $coverPicture
     *
     * @return self
     */
    public function setCoverPicture(?string $coverPicture): self
    {
        $this->coverPicture = $coverPicture;

        return $this;
    }

    /**
     * @param string|null $preview
     *
     * @return self
     */
    public function setPreview(?string $preview): self
    {
        $this->preview = $preview;

        return $this;
    }

    /**
     * @param string|null $fiveSecondVideo
     *
     * @return self
     */
    public function setFiveSecondVideo(?string $fiveSecondVideo): self
    {
        $this->fiveSecondVideo = $fiveSecondVideo;

        return $this;
    }

    /**
     * @param int $release
     *
     * @return self
     */
    public function setRelease(int $release): self
    {
        $this->release = $release;

        return $this;
    }

    /**
     * @param int|null $duration
     *
     * @return self
     */
    public function setDuration(?int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @param int|null $playCount
     *
     * @return self
     */
    public function setPlayCount(?int $playCount): self
    {
        $this->playCount = $playCount;

        return $this;
    }

    /**
     * @param int|null $voteCount
     *
     * @return self
     */
    public function setVoteCount(?int $voteCount): self
    {
        $this->voteCount = $voteCount;

        return $this;
    }

    /**
     * @param int|null $cover
     *
     * @return self
     */
    public function setCover(?int $cover): self
    {
        $this->cover = $cover;

        return $this;
    }

    /**
     * @param string|null $createdAt
     *
     * @return self
     */
    public function setCreatedAt(?string $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @param string|null $updatedAt
     *
     * @return self
     */
    public function setUpdatedAt(?string $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @param int|null $serverId
     *
     * @return self
     */
    public function setServerId(?int $serverId): self
    {
        $this->serverId = $serverId;

        return $this;
    }

    /**
     * @param int $isTransfer
     *
     * @return self
     */
    public function setIsTransfer(int $isTransfer): self
    {
        $this->isTransfer = $isTransfer;

        return $this;
    }

    /**
     * @param int $softDelete
     *
     * @return self
     */
    public function setSoftDelete(int $softDelete): self
    {
        $this->softDelete = $softDelete;

        return $this;
    }

    /**
     * @param int $dislike
     *
     * @return self
     */
    public function setDislike(int $dislike): self
    {
        $this->dislike = $dislike;

        return $this;
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getType(): ?int
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    /**
     * @return string|null
     */
    public function getOriginFileName(): ?string
    {
        return $this->originFileName;
    }

    /**
     * @return string|null
     */
    public function getFileConvertedMp4(): ?string
    {
        return $this->fileConvertedMp4;
    }

    /**
     * @return int
     */
    public function getCategories(): ?int
    {
        return $this->categories;
    }

    /**
     * @return int
     */
    public function getDownload(): ?int
    {
        return $this->download;
    }

    /**
     * @return string
     */
    public function getOriginUrl(): ?string
    {
        return $this->originUrl;
    }

    /**
     * @return int
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @return int|null
     */
    public function getDb1080p(): ?int
    {
        return $this->db1080p;
    }

    /**
     * @return string|null
     */
    public function getCoverPicture(): ?string
    {
        return $this->coverPicture;
    }

    /**
     * @return string|null
     */
    public function getPreview(): ?string
    {
        return $this->preview;
    }

    /**
     * @return string|null
     */
    public function getFiveSecondVideo(): ?string
    {
        return $this->fiveSecondVideo;
    }

    /**
     * @return int
     */
    public function getRelease(): ?int
    {
        return $this->release;
    }

    /**
     * @return int|null
     */
    public function getDuration(): ?int
    {
        return $this->duration;
    }

    /**
     * @return int|null
     */
    public function getPlayCount(): ?int
    {
        return $this->playCount;
    }

    /**
     * @return int|null
     */
    public function getVoteCount(): ?int
    {
        return $this->voteCount;
    }

    /**
     * @return int|null
     */
    public function getCover(): ?int
    {
        return $this->cover;
    }

    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    /**
     * @return string|null
     */
    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    /**
     * @return int|null
     */
    public function getServerId(): ?int
    {
        return $this->serverId;
    }

    /**
     * @return int
     */
    public function getIsTransfer(): ?int
    {
        return $this->isTransfer;
    }

    /**
     * @return int
     */
    public function getSoftDelete(): ?int
    {
        return $this->softDelete;
    }

    /**
     * @return int
     */
    public function getDislike(): ?int
    {
        return $this->dislike;
    }

}
